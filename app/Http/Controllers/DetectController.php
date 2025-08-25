<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class DetectController extends Controller
{
    public function detect(Request $request)
    {
        $text = (string) $request->input('text', '');
        $url  = (string) $request->input('url', '');
        if (mb_strlen($text) < 1) {
            return response()->json(['ok' => false, 'error' => 'No text provided'], 422);
        }

        // Reasonable server-side cap (ZeroGPT docs mention document-level detection)
        $text = mb_substr($text, 0, 50000);

        $cfg    = config('services.zerogpt');
        $key    = (string) ($cfg['key'] ?? '');
        $flavor = (string) ($cfg['flavor'] ?? 'business');
        $base   = rtrim((string) ($cfg['base'] ?? 'https://api.zerogpt.com'), '/');

        // Try ZeroGPT (Business API first, then RapidAPI if configured)
        if ($key !== '') {
            try {
                $resp = null;

                if ($flavor === 'rapidapi') {
                    // RapidAPI flavor
                    $resp = Http::timeout(25)
                        ->withHeaders([
                            'X-RapidAPI-Key' => $key,
                            'X-RapidAPI-Host'=> 'zerogpt.p.rapidapi.com',
                            'Content-Type'   => 'application/json',
                            'Accept'         => 'application/json',
                        ])->post('https://zerogpt.p.rapidapi.com/api/v1/detectText', [
                            'input_text' => $text,
                        ]);
                } else {
                    // Business API flavor
                    $endpoint = $base.'/api/v1/detectText'; // per public examples
                    $resp = Http::timeout(25)
                        ->withHeaders([
                            'x-api-key'   => $key,        // primary header used by many business APIs
                            'Content-Type'=> 'application/json',
                            'Accept'      => 'application/json',
                        ])->post($endpoint, [
                            'input_text' => $text,
                        ]);

                    // Some deployments use "apikey" header — try once more if failed
                    if ($resp->failed()) {
                        $resp = Http::timeout(25)
                            ->withHeaders([
                                'apikey'       => $key,
                                'Content-Type' => 'application/json',
                                'Accept'       => 'application/json',
                            ])->post($endpoint, [
                                'input_text' => $text,
                            ]);
                        // As a last resort: Authorization: Bearer
                        if ($resp->failed()) {
                            $resp = Http::timeout(25)
                                ->withHeaders([
                                    'Authorization'=> 'Bearer '.$key,
                                    'Content-Type' => 'application/json',
                                    'Accept'       => 'application/json',
                                ])->post($endpoint, [
                                    'input_text' => $text,
                                ]);
                        }
                    }
                }

                if ($resp && $resp->ok()) {
                    $payload = $resp->json();

                    // Example ZeroGPT schema (from public docs & samples):
                    // data.is_gpt_generated (0-100), data.is_human_written (0-100), data.feedback_message, etc.
                    $ai    = $this->num(data_get($payload, 'data.is_gpt_generated'));
                    $human = $this->num(data_get($payload, 'data.is_human_written'));
                    $msg   = (string) data_get($payload, 'data.feedback_message', '');

                    if ($ai !== null || $human !== null) {
                        $aiPct    = $ai !== null ? $ai : max(0.0, 100.0 - $human);
                        $humanPct = max(0.0, 100.0 - $aiPct);

                        // Lightweight confidence heuristic by length
                        $len   = mb_strlen($text);
                        $conf  = $len > 2500 ? 85 : ($len > 900 ? 75 : 65);

                        return response()->json([
                            'ok'         => true,
                            'humanPct'   => round($humanPct),
                            'aiPct'      => round($aiPct),
                            'confidence' => $conf,
                            'detectors'  => [[
                                'key' => 'zerogpt',
                                'label' => 'ZeroGPT',
                                'ai'   => round($aiPct),
                                'raw'  => $payload,
                            ]],
                            'note'       => $msg,
                            'source'     => 'zerogpt',
                            'url'        => $url,
                        ]);
                    }
                }
            } catch (\Throwable $e) {
                // fall through to local fallback handled on the frontend
            }
        }

        // Not configured or failed — frontend will run the local ensemble
        return response()->json([
            'ok'      => false,
            'source'  => 'none',
            'message' => 'ZeroGPT not configured or unreachable; use local ensemble fallback.',
        ], 502);
    }

    private function num($v): ?float
    {
        return is_numeric($v) ? (float)$v : null;
    }
}
