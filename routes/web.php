<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;
use App\Http\Controllers\AnalyzeController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('home'); // resources/views/home.blade.php
})->name('home');

/**
 * Analyzer endpoints
 * - UI first tries GET /analyze-json
 * - then POST /analyze (CSRF)
 * - then GET /analyze as fallback
 */
Route::get('/analyze-json', [AnalyzeController::class, 'analyzeJson'])->name('analyze.json');
Route::match(['POST', 'GET'], '/analyze', [AnalyzeController::class, 'analyze'])->name('analyze');

/**
 * PageSpeed Insights proxy (keeps API key hidden on server)
 * GET /api/pagespeed?url=https://example.com&strategy=mobile|desktop
 */
Route::get('/api/pagespeed', function (Request $request) {
    $url = $request->query('url', '');
    $strategy = $request->query('strategy', 'mobile');

    if (!$url) {
        return response()->json(['error' => 'Missing url'], 422);
    }

    $apiKey = config('services.google.pagespeed.key') ?: env('GOOGLE_PAGESPEED_API_KEY');
    if (!$apiKey) {
        return response()->json(['error' => 'PageSpeed API key missing on server'], 500);
    }

    $endpoint = 'https://www.googleapis.com/pagespeedonline/v5/runPagespeed';

    try {
        $resp = Http::timeout(30)->get($endpoint, [
            'url'      => $url,
            'strategy' => $strategy, // 'mobile' or 'desktop'
            'category' => ['performance', 'accessibility', 'best-practices', 'seo'],
            'key'      => $apiKey,
        ]);

        if (!$resp->ok()) {
            return response()->json([
                'error'  => 'PSI error',
                'status' => $resp->status(),
                'body'   => $resp->json(),
            ], $resp->status());
        }

        return response()->json($resp->json());
    } catch (\Throwable $e) {
        return response()->json(['error' => 'PSI request failed', 'message' => $e->getMessage()], 500);
    }
})->name('pagespeed.proxy');

/* Optional quick health check */
Route::get('/ping', fn () => response()->json(['ok' => true, 'time' => now()->toIso8601String()]));
