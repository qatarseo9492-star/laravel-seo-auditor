<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class DetectController extends Controller
{
    /**
     * POST /api/detect
     * Body: { text: string, url?: string }
     * Returns: { ok, aiPct, humanPct, confidence, language, detectors: [{key,label,ai}] }
     * Works with NO API keys: falls back to local multi-detector on server.
     */
    public function __invoke(Request $req)
    {
        $text = (string) $req->input('text', '');
        $url  = (string) $req->input('url', '');

        if (mb_strlen($text) < 40) {
            return response()->json([
                'ok'    => false,
                'error' => 'Not enough text to analyze (min 40 chars)'
            ], 422);
        }

        $lang = $this->guessLang($text);
        $timeout = (int) env('DETECT_TIMEOUT_SECONDS', 8);

        $detectors = [];
        $externalAllowed = filter_var(env('DETECT_ALLOW_EXTERNAL', true), FILTER_VALIDATE_BOOLEAN);
        $forceLocal      = filter_var(env('DETECT_FORCE_LOCAL', false), FILTER_VALIDATE_BOOLEAN);

        /* ===================== 1) Try external providers (optional) ===================== */
        if ($externalAllowed && !$forceLocal) {
            // ZeroGPT
            if (env('ZEROGPT_API_URL') && env('ZEROGPT_API_KEY')) {
                try {
                    $r = Http::timeout($timeout)
                        ->withHeaders([
                            'Authorization' => 'Bearer ' . env('ZEROGPT_API_KEY'),
                            'Accept' => 'application/json'
                        ])
                        ->post(env('ZEROGPT_API_URL'), [
                            'input_text' => $text
                        ]);
                    if ($r->ok()) {
                        $j = $r->json();
                        $aiPct =
                            (isset($j['ai_probability']) ? $j['ai_probability'] * 100 : null) ??
                            ($j['data']['ai'] ?? null) ??
                            ($j['score']['aiPct'] ?? null);
                        if (is_numeric($aiPct)) {
                            $detectors[] = ['key'=>'zerogpt','label'=>'ZeroGPT','ai'=>$this->clampInt($aiPct)];
                        }
                    }
                } catch (\Throwable $e) { /* ignore */ }
            }

            // GPTZero
            if (env('GPTZERO_API_URL') && env('GPTZERO_API_KEY')) {
                try {
                    $r = Http::timeout($timeout)
                        ->withHeaders([
                            'Authorization' => 'Bearer ' . env('GPTZERO_API_KEY'),
                            'Accept'        => 'application/json',
                            'Content-Type'  => 'application/json',
                        ])
                        ->post(env('GPTZERO_API_URL'), ['document' => ['text' => $text]]);
                    if ($r->ok()) {
                        $j = $r->json();
                        $aiProb = $j['documents'][0]['average_generated_prob'] ?? ($j['overall']['fake_probability'] ?? null);
                        if (is_numeric($aiProb)) {
                            $detectors[] = ['key'=>'gptzero','label'=>'GPTZero','ai'=>$this->clampInt($aiProb*100)];
                        }
                    }
                } catch (\Throwable $e) { /* ignore */ }
            }

            // OriginalityAI
            if (env('ORIGINALITY_API_URL') && env('ORIGINALITY_API_KEY')) {
                try {
                    $r = Http::timeout($timeout)
                        ->withHeaders([
                            'x-api-key' => env('ORIGINALITY_API_KEY'),
                            'Accept'    => 'application/json',
                        ])
                        ->post(env('ORIGINALITY_API_URL'), [
                            'content'  => $text,
                            'language' => $lang['code'] ?? 'auto',
                        ]);
                    if ($r->ok()) {
                        $j = $r->json();
                        $aiProb = ($j['ai'] ?? null) ?? ($j['score']['ai'] ?? null);
                        $aiPct  = (is_numeric($aiProb) && $aiProb <= 1) ? $aiProb * 100 : ($j['ai_score'] ?? null);
                        if (is_numeric($aiPct)) {
                            $detectors[] = ['key'=>'originality','label'=>'OriginalityAI','ai'=>$this->clampInt($aiPct)];
                        }
                    }
                } catch (\Throwable $e) { /* ignore */ }
            }
        }

        /* ===================== 2) Local multi-detector (no keys required) ===================== */
        if (empty($detectors)) {
            $s = $this->stats($text);

            // a) Stylometry (lexical variety + burstiness)
            $stylAi = $this->clampInt(
                10
                + $this->scaleLow($s['cov'], 0.45, 25)    // lower CoV -> more AI
                + $this->scaleLow($s['ttr'], 0.45, 18)    // lower TTR -> more AI
            );

            // b) Burstiness (sentence length variance)
            $burstAi = $this->clampInt($this->scaleLow($s['cov'], 0.50, 80));

            // c) Repetition (tri-gram repeats)
            $repAi = $this->clampInt(100 * min(1, $s['triRepeatRatio'] / 0.15));

            // d) Entropy (char entropy; unusually low -> templated)
            $entropyAi = $this->clampInt(
                ($s['entropy'] < 3.5 ? (3.5 - $s['entropy']) * 80 : 0)
                + ($s['entropy'] > 5.0 ? ($s['entropy'] - 5.0) * 30 : 0)
            );

            // e) Readability extremes (too hard or too easy)
            $readAi = $this->clampInt(
                ($s['grade'] > 16 ? ($s['grade'] - 16) * 6 : 0)
                + ($s['grade'] < 4  ? (4 - $s['grade']) * 8 : 0)
            );

            $detectors = [
                ['key'=>'stylometry','label'=>'Stylometry','ai'=>$stylAi],
                ['key'=>'burstiness','label'=>'Burstiness','ai'=>$burstAi],
                ['key'=>'repetition','label'=>'Repetition','ai'=>$repAi],
                ['key'=>'entropy','label'=>'Entropy','ai'=>$entropyAi],
                ['key'=>'readability','label'=>'Readability','ai'=>$readAi],
            ];
        }

        /* ===================== 3) Aggregate ===================== */
        $vals = array_map(fn($d)=> (int) $d['ai'], $detectors);
        sort($vals);
        $n = count($vals);
        $median = ($n % 2 === 1) ? $vals[intval($n/2)] : intval(round(($vals[$n/2 - 1] + $vals[$n/2]) / 2));

        $spread = max($vals) - min($vals);
        $agreement = 1 - ($spread / 100);             // 0..1
        $wc = $this->wordCount($text);
        $wcBoost = max(0, min(20, log(max(1, $wc)) * 4));
        $confidence = (int) round(min(95, max(55, 50 + $n * 8 + $agreement * 20 + $wcBoost - $spread * 0.20)));

        return response()->json([
            'ok'         => true,
            'language'   => $lang,
            'aiPct'      => $median,
            'humanPct'   => 100 - $median,
            'confidence' => $confidence,
            'detectors'  => $detectors,
            'source'     => empty(array_filter($detectors, fn($d)=>in_array($d['key'], ['zerogpt','gptzero','originality'], true))) ? 'local' : 'external+local'
        ]);
    }

    /* -------------------- Helpers -------------------- */

    private function clampInt($x){ return max(0, min(100, (int) round($x))); }

    private function scaleLow($v, $pivot, $maxGain){
        // higher gain the further below pivot
        if (!is_numeric($v)) return 0;
        return max(0, min($maxGain, ($pivot - $v) / max(0.0001, $pivot) * $maxGain));
    }

    private function wordCount($text){
        preg_match_all('/[A-Za-z\p{L}\']+/u', $text, $m);
        return count($m[0] ?? []);
    }

    /** Language guess via Unicode ranges */
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

    /** Compute text stats for local detectors */
    private function stats(string $text): array
    {
        $t = preg_replace('/\s+/u', ' ', trim(str_replace("\xC2\xA0", ' ', $text)));
        preg_match_all('/[A-Za-z\p{L}0-9\']+/u', $t, $wm); $words = array_map('mb_strtolower', $wm[0] ?? []);
        $tokens = count($words);
        $sents  = preg_split('/(?<=[.!?])\s+|\n+(?=\S)/u', $t, -1, PREG_SPLIT_NO_EMPTY);
        $sents  = $sents ?: [$t];

        // sentence lengths
        $lens = [];
        foreach ($sents as $s) {
            preg_match_all('/[A-Za-z\p{L}0-9\']+/u', $s, $m);
            $lens[] = count($m[0] ?? []);
        }
        $lens = array_filter($lens, fn($v)=>$v>0);
        $mean = $lens ? array_sum($lens)/count($lens) : 0;
        $var  = $lens ? array_sum(array_map(fn($x)=>pow($x-$mean,2), $lens))/count($lens) : 0;
        $cov  = $mean ? sqrt($var)/$mean : 0;

        // TTR
        $types = $tokens ? count(array_unique($words)) : 0;
        $ttr   = $tokens ? $types / $tokens : 0;

        // tri-gram repetition
        $tri = []; $triT=0; $triRep=0;
        for ($i=0; $i < $tokens-2; $i++) {
            $g = $words[$i].' '.$words[$i+1].' '.$words[$i+2];
            $tri[$g] = ($tri[$g] ?? 0) + 1; $triT++;
        }
        foreach ($tri as $k=>$v) { if ($v>1) $triRep += ($v-1); }
        $triRepeatRatio = $triT ? $triRep / $triT : 0;

        // Flesch-Kincaid Grade (approx)
        $syll = 0;
        foreach ($words as $w) { $syll += $this->countSyll($w); }
        $sentN = max(1, count($sents));
        $grade = 0.39 * (($tokens?:1)/$sentN) + 11.8 * ($tokens? ($syll/$tokens) : 0) - 15.59;

        // Char entropy
        $chars = preg_replace('/\s+/u', '', mb_strtolower($t));
        $freq = [];
        $len  = mb_strlen($chars);
        for ($i=0; $i<$len; $i++) {
            $c = mb_substr($chars, $i, 1);
            $freq[$c] = ($freq[$c] ?? 0) + 1;
        }
        $entropy = 0.0;
        if ($len > 0) {
            foreach ($freq as $c=>$n) {
                $p = $n / $len;
                $entropy += -$p * log($p, 2); // bits/char
            }
        }

        return [
            'tokens' => $tokens,
            'cov' => $cov,
            'ttr' => $ttr,
            'triRepeatRatio' => $triRepeatRatio,
            'grade' => max(0, min(22, $grade)),
            'entropy' => $entropy,
        ];
    }

    /** Minimal syllable heuristic for Latin scripts */
    private function countSyll(string $w): int
    {
        $w = mb_strtolower(preg_replace('/[^a-z]/i', '', $w));
        if ($w === '') return 0;
        preg_match_all('/[aeiouy]+/i', $w, $m);
        $mcount = count($m[0] ?? []);
        if (preg_match('/(ed|es)$/', $w)) $mcount--;
        if (preg_match('/^y/', $w)) $mcount--;
        return max(1, $mcount);
    }
}
