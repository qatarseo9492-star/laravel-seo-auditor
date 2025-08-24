<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class CruxController extends Controller
{
    public function url(Request $request)
    {
        $url = $request->query('url');
        if (!$url) return response()->json(['ok'=>false,'error'=>'Missing url'],422);
        if (!preg_match('~^https?://~i',$url)) $url = 'https://'.ltrim($url,'/');

        $key = config('services.psi.key'); // reuse same key; enable Chrome UX Report API in Google Cloud
        if (!$key) return response()->json(['ok'=>false,'error'=>'API key not configured'],500);

        $cacheKey = 'crux:url:'.md5($url);
        if (Cache::has($cacheKey)) return response()->json(Cache::get($cacheKey) + ['cached'=>true]);

        try {
            $body = ['url' => $url]; // You can switch to ['origin' => 'https://example.com'] for origin-level data
            $resp = Http::timeout(12)
                ->withHeaders(['Content-Type'=>'application/json'])
                ->post('https://chromeuxreport.googleapis.com/v1/records:queryRecord?key='.$key, $body);

            if (!$resp->ok()) {
                return response()->json([
                    'ok'=>false,'code'=>$resp->status(),'error'=>'CrUX request failed','body'=>$resp->json()
                ], 502);
            }

            $data = $resp->json();

            // Extract p75 values safely (ms for LCP/INP/FCP/TTFB; unitless for CLS)
            $m = $data['record']['metrics'] ?? [];
            $p75 = fn($k) => $m[$k]['percentiles']['p75'] ?? null;

            $result = [
                'ok'   => true,
                'type' => ($data['record']['key']['url']??null) ? 'url' : 'origin',
                'p75'  => [
                    'LCP' => $p75('largest_contentful_paint'),      // ms
                    'INP' => $p75('interaction_to_next_paint'),      // ms
                    'CLS' => $p75('cumulative_layout_shift'),        // unitless * 100 likely; some responses already unitless
                    'FCP' => $p75('first_contentful_paint') ?? null, // ms
                    'TTFB'=> $p75('experimental_time_to_first_byte') ?? null, // ms (experimental)
                ],
                'raw'  => $data,
                'cached' => false,
            ];

            Cache::put($cacheKey, $result, now()->addHours(24)); // CrUX updates daily/monthly
            return response()->json($result);

        } catch (\Throwable $e) {
            return response()->json(['ok'=>false,'error'=>'Exception: '.$e->getMessage()],500);
        }
    }
}
