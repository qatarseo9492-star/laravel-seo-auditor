<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;
use App\Http\Controllers\AnalyzeController;

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

/* Optional quick health check */
Route::get('/ping', fn() => response()->json(['ok' => true, 'time' => now()->toIso8601String()]));

/**
 * PSI Proxy (keeps API key hidden)
 * Usage: /api/psi?url=https://example.com&strategy=mobile
 *
 * Reads your key from config/services.php → 'google' => ['key' => env('GOOGLE_API_KEY')]
 */
Route::get('/api/psi', function (Request $request) {
    $url = trim($request->query('url', ''));
    if (!$url) {
        return response()->json(['ok' => false, 'error' => 'Missing url parameter'], 422);
    }
    // Normalize URL
    if (!preg_match('~^https?://~i', $url)) {
        $url = 'https://' . ltrim($url, '/');
    }
    $strategy = $request->query('strategy', 'mobile');
    if (!in_array($strategy, ['mobile', 'desktop'], true)) {
        $strategy = 'mobile';
    }

    $key = config('services.google.key');
    if (!$key) {
        return response()->json(['ok' => false, 'error' => 'Google API key not configured'], 500);
    }

    $endpoint = 'https://www.googleapis.com/pagespeedonline/v5/runPagespeed';
    $params = [
        'url'       => $url,
        'strategy'  => $strategy,
        // Ask for more categories (not all are always returned by PSI)
        'category'  => ['performance', 'seo', 'best-practices', 'accessibility'],
        'utm_source'=> 'semantic-seo-master',
        'key'       => $key,
    ];

    try {
        $resp = Http::timeout(20)->retry(2, 300)->get($endpoint, $params);
        if (!$resp->ok()) {
            return response()->json([
                'ok' => false,
                'error' => 'PSI error',
                'status' => $resp->status(),
                'body' => $resp->json()
            ], 502);
        }
        return response()->json(['ok' => true, 'strategy' => $strategy, 'data' => $resp->json()]);
    } catch (\Throwable $e) {
        return response()->json(['ok' => false, 'error' => $e->getMessage()], 500);
    }
})->name('psi.proxy');
