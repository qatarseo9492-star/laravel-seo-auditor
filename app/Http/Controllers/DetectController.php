<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class DetectController extends Controller
{
    /**
     * POST /api/detect
     * Body: { text: string, url?: string }
     * Returns: { ok, aiPct, humanPct, confidence, language?, detectors: [{key,label,ai}] }
     */
    public function __invoke(Request $req)
    {
        $text = (string) $req->input('text', '');
        $url  = (string) $req->input('url', '');

        if (mb_strlen($text) < 40) {
            return response()->json([
                'ok' => false,
                'error' => 'Not enough text to analyze'
            ], 422);
        }

        // --- Simple multilingual hint (very light heuristic; you can swap for a lib) ---
        $lang = $this->guessLang($text);

        $detectors = [];

        // Timeouts & headers
        $timeout = (int) env('DETECT_TIMEOUT_SECONDS', 8);

        // === ZeroGPT (example wiring; make sure API_URL + KEY are correct for your plan) ===
        if (env('ZEROGPT_API_URL') && env('ZEROGPT_API_KEY')) {
            try {
                $r = Http::timeout($timeout)
                    ->withHeaders([
                        'Authorization' => 'Bearer ' . env('ZEROGPT_API_KEY'),
                        'Accept' => 'application/json'
                    ])
                    ->post(env('ZEROGPT_API_URL'), [
                        'input_text' => $text,
                        // adjust to the provider’s expected payload
                    ]);

                if ($r->ok()) {
                    $j = $r->json();

                    // Try the most common shapes; adapt as needed for your contract:
                    // 1) { "ai_probability": 0.63 }       -> 63
                    // 2) { "data": { "ai": 64 } }         -> 64
                    // 3) { "score": { "aiPct": 62 } }     -> 62
                    $aiPct =
                        (isset($j['ai_probability']) ? $j['ai_probability'] * 100 : null) ??
                        ($j['data']['ai'] ?? null) ??
                        ($j['score']['aiPct'] ?? null);

                    if (is_numeric($aiPct)) {
                        $detectors[] = [
                            'key'   => 'zerogpt',
                            'label' => 'ZeroGPT',
                            'ai'    => max(0, min(100, (int) round($aiPct))),
                        ];
                    }
                }
            } catch (\Throwable $e) {
                // swallow; we’ll fallback if all fail
            }
        }

        // === GPTZero (official/rapid API contract differs; adapt endpoint/payload) ===
        if (env('GPTZERO_API_URL') && env('GPTZERO_API_KEY')) {
            try {
                $r = Http::timeout($timeout)
                    ->withHeaders([
                        'Authorization' => 'Bearer ' . env('GPTZERO_API_KEY'),
                        'Accept'        => 'application/json',
                        'Content-Type'  => 'application/json',
                    ])
                    ->post(env('GPTZERO_API_URL'), [
                        'document' => ['text' => $text],
                    ]);

                if ($r->ok()) {
                    $j = $r->json();
                    // examples to normalize:
                    // { "documents":[{"average_generated_prob":0.61}] }
                    // or { "overall": { "fake_probability": 0.59 } }
                    $aiProb = $j['documents'][0]['average_generated_prob'] ?? ($j['overall']['fake_probability'] ?? null);
                    if (is_numeric($aiProb)) {
                        $detectors[] = [
                            'key'   => 'gptzero',
                            'label' => 'GPTZero',
                            'ai'    => max(0, min(100, (int) round($aiProb * 100))),
                        ];
                    }
                }
            } catch (\Throwable $e) { /* ignore */ }
        }

        // === OriginalityAI ===
        if (env('ORIGINALITY_API_URL') && env('ORIGINALITY_API_KEY')) {
            try {
                $r = Http::timeout($timeout)
                    ->withHeaders([
                        'x-api-key' => env('ORIGINALITY_API_KEY'),
                        'Accept'    => 'application/json',
                    ])
                    ->post(env('ORIGINALITY_API_URL'), [
                        'content' => $text,
                        // Adjust as needed: some plans require language param
                        'language' => $lang['code'] ?? 'auto',
                    ]);

                if ($r->ok()) {
                    $j = $r->json();
                    // examples:
                    // { "ai": 0.64 } or { "score": {"ai": 0.64 } } or { "ai_score": 64 }
                    $aiProb =
                        ($j['ai'] ?? null) ??
                        ($j['score']['ai'] ?? null);
                    $aiPct =
                        (is_numeric($aiProb) && $aiProb <= 1) ? $aiProb * 100 :
                        ($j['ai_score'] ?? null);

                    if (is_numeric($aiPct)) {
                        $detectors[] = [
                            'key'   => 'originality',
                            'label' => 'OriginalityAI',
                            'ai'    => max(0, min(100, (int) round($aiPct))),
                        ];
                    }
                }
            } catch (\Throwable $e) { /* ignore */ }
        }

        if (empty($detectors)) {
            // Nothing came back -> tell frontend to run local ensemble
            return response()->json([
                'ok'    => false,
                'error' => 'No detectors available or all timed out',
                'language' => $lang,
            ], 503);
        }

        // Aggregate: median is robust to outliers
        $vals = array_map(fn($d) => (int) $d['ai'], $detectors);
        sort($vals);
        $n = count($vals);
        $median = ($n % 2 === 1) ? $vals[intval($n/2)] : intval(round(($vals[$n/2 - 1] + $vals[$n/2]) / 2));

        // Confidence: based on num detectors + agreement
        $spread = max($vals) - min($vals);       // 0..100
        $agreement = 1 - ($spread / 100);        // 0..1
        $confidence = (int) round(min(95, max(55, 50 + $n * 10 + $agreement * 20 - $spread * 0.15)));

        return response()->json([
            'ok'         => true,
            'language'   => $lang,
            'aiPct'      => $median,
            'humanPct'   => 100 - $median,
            'confidence' => $confidence,
            'detectors'  => $detectors,
        ]);
    }

    /** Very light language guess (Unicode ranges) */
    private function guessLang(string $t): array
    {
        $code = 'en';
        if (preg_match('/\p{Arabic}/u', $t))       $code = 'ar';
        elseif (preg_match('/\p{Cyrillic}/u', $t)) $code = 'ru';
        elseif (preg_match('/\p{Han}|\p{Hiragana}|\p{Katakana}/u', $t)) $code = 'zh';
        elseif (preg_match('/\p{Devanagari}/u', $t)) $code = 'hi';
        elseif (preg_match('/\p{Hebrew}/u', $t))   $code = 'he';
        elseif (preg_match('/\p{Thai}/u', $t))     $code = 'th';
        elseif (preg_match('/\p{Hangul}/u', $t))   $code = 'ko';
        elseif (preg_match('/\p{Greek}/u', $t))    $code = 'el';

        return ['code' => $code, 'confidence' => 0.9];
    }
}
