<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AnalyzerController;

/*
|--------------------------------------------------------------------------
| API Routes  (/api/*)
|--------------------------------------------------------------------------
*/

Route::get('/status', function () {
    return response()->json([
        'ok'      => true,
        'service' => 'Semantic SEO Master Analyzer API',
        'version' => '1.0',
        'time'    => now()->toIso8601String(),
    ]);
})->name('api.status');

Route::middleware('throttle:seoapi')->group(function () {
    // Main content analysis
    Route::post('/semantic-analyze', [AnalyzerController::class, 'analyze'])
        ->name('api.semantic');

    // Technical SEO analysis
    Route::post('/technical-seo-analyze', [AnalyzerController::class, 'analyzeTechnicalSeo'])
        ->name('api.technical-seo');
        
    // Keyword Intelligence analysis
    Route::post('/keyword-analyze', [AnalyzerController::class, 'analyzeKeywords'])
        ->name('api.keyword-analyze');

    // NEW: Content Analysis Engine
    Route::post('/content-engine-analyze', [AnalyzerController::class, 'analyzeContentEngine'])
        ->name('api.content-engine');

    // Optional stubs (safe to keep)
    Route::post('/ai-check', [AnalyzerController::class, 'aiCheck'])->name('api.aicheck')
        ->withoutMiddleware('throttle:seoapi');
    Route::post('/topic-cluster', [AnalyzerController::class, 'topicClusterAnalyze'])->name('api.topiccluster')
        ->withoutMiddleware('throttle:seoapi');
});

// CORS preflight
Route::options('/{any}', fn () => response()->noContent(204))->where('any', '.*');

