<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class CruxController extends Controller
{
    public function url(Request $request)
    {
        $target = $request->query('url');
        if (!$target) {
            return response()->json(['ok' => false, 'error' => 'Missing url'], 422);
        }

        $key = config('services.google.page_speed_key') ?? env('GOOGLE_API_KEY');
        if (!$key) {
            return response()->json(['ok' => false, 'error' => 'Missing Google API key'], 500);
        }

        $endpoint = 'https://chromeuxreport.googleapis.com/v1/records:queryRecord?key='.$key;

        // Try URL-level first
        $respUrl = Http::post($endpoint, ['url' => $target]);
        if ($respUrl->ok() && ($rec = $respUrl->json('record'))) {
            return response()->json($this->shapeCrux($rec, 'url'));
        }

        // Fallback to origin-level
        $host = parse_url($target, PHP_URL_HOST);
        $scheme = parse_url($target, PHP_URL_SCHEME) ?: 'https';
        $origin = $scheme.'://'.$host;
        $respOrigin = Http::post($endpoint, ['origin' => $origin]);
        if ($respOrigin->ok() && ($rec = $respOrigin->json('record'))) {
            return response()->json($this->shapeCrux($rec, 'origin'));
        }

        return response()->json(['ok' => false, 'error' => 'CrUX not available for this URL/origin'], 404);
    }

    private function shapeCrux(array $record, string $type)
    {
        $metrics = $record['metrics'] ?? [];
        $get = fn($k) => $metrics[$k]['p75'] ?? null;

        // CrUX p75 values are given in ms for LCP & INP; CLS is unitless.
        $p75 = [
            'LCP' => $get('largest_contentful_paint'),
            'INP' => $get('interaction_to_next_paint'),
            'CLS' => $get('cumulative_layout_shift'),
        ];

        return [
            'ok' => true,
            'type' => $type,
            'p75' => $p75,
        ];
    }
}
