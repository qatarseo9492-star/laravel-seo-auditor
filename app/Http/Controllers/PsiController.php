<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class PsiController extends Controller
{
    public function run(Request $request)
    {
        $url = $request->query('url');
        $strategy = $request->query('strategy', 'mobile'); // 'mobile' or 'desktop'

        if (!$url) {
            return response()->json(['ok' => false, 'error' => 'Missing url'], 422);
        }

        $key = config('services.google.page_speed_key') ?? env('GOOGLE_API_KEY');
        if (!$key) {
            return response()->json(['ok' => false, 'error' => 'Missing Google API key'], 500);
        }

        $api = 'https://www.googleapis.com/pagespeedonline/v5/runPagespeed';

        $resp = Http::get($api, [
            'url'       => $url,
            'strategy'  => $strategy,
            'category'  => ['PERFORMANCE','SEO','ACCESSIBILITY','BEST_PRACTICES'],
            'key'       => $key,
        ]);

        if (!$resp->ok()) {
            $err = $resp->json('error.message') ?? 'PSI error';
            return response()->json(['ok' => false, 'error' => $err], $resp->status());
        }

        $json = $resp->json();

        return response()->json([
            'ok' => true,
            'strategy' => $strategy,
            'analysisUTCTimestamp' => $json['analysisUTCTimestamp'] ?? null,
            'lighthouseResult'     => $json['lighthouseResult'] ?? null,
        ]);
    }
}
