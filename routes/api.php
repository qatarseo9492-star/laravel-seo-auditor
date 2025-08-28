<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AnalyzerController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
| All routes here are auto-prefixed with /api.
| Example: POST /api/semantic-analyze
*/

/**
 * Health check
 */
Route::get('/status', function () {
    return response()->json([
        'ok'      => true,
        'service' => 'Semantic SEO Master Analyzer API',
        'version' => '1.0',
        'time'    => now()->toIso8601String(),
    ]);
})->name('api.status');

/**
 * Main analyzer endpoints
 * Uses a named rate limiter `seoapi` (configured in RouteServiceProvider)
 */
Route::middleware('throttle:seoapi')->group(function () {
    Route::post('/semantic-analyze', [AnalyzerController::class, 'semanticAnalyze'])->name('api.semantic');
    Route::post('/ai-check',         [AnalyzerController::class, 'aiCheck'])->name('api.aicheck');
    Route::post('/topic-cluster',    [AnalyzerController::class, 'topicClusterAnalyze'])->name('api.topiccluster');
});

/**
 * Optional: CORS preflight catch-all for /api/*
 * (Safe to keep even if you also handle CORS via middleware.)
 */
Route::options('/{any}', function () {
    return response()->noContent(204);
})->where('any', '.*');
