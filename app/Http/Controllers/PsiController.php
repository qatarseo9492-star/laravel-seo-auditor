<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class PsiController extends Controller
{
    /**
     * Proxy Google PageSpeed Insights v5 safely via server.
     *
     * Example:
     *   GET /api/psi?url=https://example.com&strategy=mobile&category[]=performance&category[]=accessibility&category[]=seo&category[]=best-practices
     *
     * Reads API key from: config('services.pagespeed.key')  // .env: PAGESPEED_API_KEY
     * Optional config (services.php or .env):
     *   services.pagespeed.endpoint (default: https://www.googleapis.com/pagespeedonline/v5/runPagespeed)
     *   services.pagespeed.timeout  (default: 20 seconds)
     *   services.pagespeed.cache_ttl (default: 60 seconds)
     */
    public function run(Request $request)
    {
        // 1) Validate inputs (keep responses JSON)
        $request->validate([
            'url'       => ['required', 'url'],
            'strategy'  => ['nullable', 'in:mobile,desktop'],
            'category'  => ['sometimes', 'array'],
            'category.*'=> ['in:performance,accessibility,seo,best-practices,Best Practices,Best%20Practices'],
        ]);

        $url      = (string) $request->query('url');
        $strategy = (string) $request->query('strategy', 'mobile');

        // 2) Normalize categories (accept array, string, or none)
        $catsIn = $request->query('category', []);
        if (!is_array($catsIn)) {
            $catsIn = strlen((string) $catsIn) ? array_map('trim', explode(',', (string) $catsIn)) : [];
        }
        $categories = [];
        foreach ($catsIn as $c) {
            $c = trim((string) $c);
            if ($c === 'Best Practices' || $c === 'Best%20Practices') {
                $c = 'best-practices';
            } else {
                $c = strtolower($c);
            }
            if (in_array($c, ['performance','accessibility','seo','best-practices'], true)) {
                $categories[] = $c;
            }
        }
        if (empty($categories)) {
            $categories = ['performance','accessibility','seo','best-practices'];
        }

        // 3) Config & key
        $endpoint = config('services.pagespeed.endpoint', 'https://www.googleapis.com/pagespeedonline/v5/runPagespeed');
        $timeout  = (int) config('services.pagespeed.timeout', 20);
        $ttl      = (int) config('services.pagespeed.cache_ttl', 60);
        $apiKey   = (string) config('services.pagespeed.key', env('PAGESPEED_API_KEY', ''));

        if ($apiKey === '') {
            // Key is recommended to avoid hard quota, but not strictly required.
            // We still proceed without a key; Google will apply anonymous quota.
            Log::info('PSI proxy: proceeding without API key');
        }

        // 4) Build query (repeat "category" as array to get repeated query params)
        $query = [
            'url'      => $url,
            'strategy' => $strategy,
            'category' => array_values(array_unique($categories)),
        ];
        if ($apiKey !== '') {
            $query['key'] = $apiKey;
        }

        // 5) Cache + fetch with small retry
        $cacheKey = 'psi:' . md5(json_encode([$endpoint, $query]));
        try {
            $payload = Cache::remember($cacheKey, now()->addSeconds($ttl), function () use ($endpoint, $timeout, $query) {
                $resp = Http::timeout($timeout)
                    ->retry(1, 250) // one retry with 250ms backoff for transient 429/5xx
                    ->acceptJson()
                    ->get($endpoint, $query);

                if ($resp->successful()) {
                    return $resp->json();
                }

                return [
                    'proxy_error' => true,
                    'status'      => $resp->status(),
                    'body'        => $resp->json() ?? $resp->body(),
                ];
            });

            // Return raw PSI JSON structure so the frontend can read lighthouseResult, loadingExperience, etc.
            return response()->json($payload)->header('Access-Control-Allow-Origin', '*');
        } catch (\Throwable $e) {
            Log::warning('PSI proxy failure', ['msg' => $e->getMessage(), 'url' => $url]);
            return response()->json([
                'proxy_error' => true,
                'message'     => 'Proxy request failed. See server logs for details.',
            ], 500)->header('Access-Control-Allow-Origin', '*');
        }
    }
}
