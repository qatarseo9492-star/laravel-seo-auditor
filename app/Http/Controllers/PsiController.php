<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class PsiController extends Controller
{
    public function run(Request $request)
    {
        $url = $request->query('url');
        if (! $url) {
            return response()->json(['ok' => false, 'error' => 'Missing url'], 422);
        }

        // Normalize & basic SSRF guard
        if (!preg_match('~^https?://~i', $url)) {
            $url = 'https://' . ltrim($url, '/');
        }

        $key = config('services.psi.key'); // reads PSI_API_KEY from config/services.php
        if (!$key) {
            return response()->json(['ok' => false, 'error' => 'PSI key not configured'], 500);
        }

        try {
            $params = [
                'url'      => $url,
                'strategy' => $request->query('strategy', 'mobile'), // or 'desktop'
                'category' => ['PERFORMANCE','ACCESSIBILITY','BEST_PRACTICES','SEO'],
                'locale'   => $request->query('locale', 'en_US'),
                'key'      => $key,
            ];

            $resp = Http::timeout(25)->acceptJson()
                ->get('https://www.googleapis.com/pagespeedonline/v5/runPagespeed', $params);

            if (!$resp->ok()) {
                return response()->json([
                    'ok' => false,
                    'code' => $resp->status(),
                    'error' => 'PSI request failed',
                    'body' => $resp->json(),
                ], 502);
            }

            $json = $resp->json();

            return response()->json([
                'ok' => true,
                'lighthouseResult' => $json['lighthouseResult'] ?? null,
                'loadingExperience' => $json['loadingExperience'] ?? null,
                'originLoadingExperience' => $json['originLoadingExperience'] ?? null,
                'analysisUTCTimestamp' => $json['analysisUTCTimestamp'] ?? null,
            ]);
        } catch (\Throwable $e) {
            return response()->json(['ok' => false, 'error' => 'Exception: '.$e->getMessage()], 500);
        }
    }
}
