<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class PsiController extends Controller
{
    public function run(Request $request)
    {
        $url = $request->query('url');
        if (!$url) return response()->json(['ok'=>false,'error'=>'Missing url'],422);
        if (!preg_match('~^https?://~i',$url)) $url = 'https://'.ltrim($url,'/');

        $strategy = $request->query('strategy','mobile'); // mobile|desktop
        $ttlMin   = (int) $request->query('ttl', 30);     // cache minutes (default 30)
        $refresh  = (bool) $request->query('refresh', false);

        $key = config('services.psi.key');
        if (!$key) return response()->json(['ok'=>false,'error'=>'PSI key not configured'],500);

        $cacheKey = 'psi:'.md5($url.'|'.$strategy);
        if (!$refresh && Cache::has($cacheKey)) {
            $cached = Cache::get($cacheKey);
            return response()->json($cached + ['cached'=>true]);
        }

        try {
            $params = [
                'url'      => $url,
                'strategy' => $strategy,
                'category' => ['PERFORMANCE','ACCESSIBILITY','BEST_PRACTICES','SEO'],
                'locale'   => $request->query('locale','en_US'),
                'key'      => $key,
            ];

            $resp = Http::timeout(30)->acceptJson()
                ->get('https://www.googleapis.com/pagespeedonline/v5/runPagespeed', $params);

            if (!$resp->ok()) {
                return response()->json([
                    'ok'=>false,'code'=>$resp->status(),'error'=>'PSI request failed','body'=>$resp->json()
                ], 502);
            }

            $json = [
                'ok' => true,
                'lighthouseResult'       => $resp['lighthouseResult'] ?? null,
                'loadingExperience'      => $resp['loadingExperience'] ?? null,
                'originLoadingExperience'=> $resp['originLoadingExperience'] ?? null,
                'analysisUTCTimestamp'   => $resp['analysisUTCTimestamp'] ?? null,
                'cached'                 => false,
            ];

            Cache::put($cacheKey, $json, now()->addMinutes(max(5,$ttlMin)));
            return response()->json($json);

        } catch (\Throwable $e) {
            return response()->json(['ok'=>false,'error'=>'Exception: '.$e->getMessage()],500);
        }
    }
}
