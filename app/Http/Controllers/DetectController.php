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
                        if (i
