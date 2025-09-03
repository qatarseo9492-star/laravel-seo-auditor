<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AnalyzerController;

/*
|--------------------------------------------------------------------------
| API Routes  (/api/*)
|--------------------------------------------------------------------------
|
| These routes are stateless and are typically protected by throttling
| and other API-specific middleware.
|
*/

// A simple health-check endpoint
Route::get('/status', function () {
    return response()->json([
        'ok'      => true,
        'service' => 'Semantic SEO Master Analyzer API',
        'version' => '2.0', // Updated version
        'time'    => now()->toIso8601String(),
    ]);
})->name('api.status');

// Group all analysis endpoints under a throttle middleware to prevent abuse
Route::middleware('throttle:seoapi')->group(function () {
    
    // 1. Initial Local HTML Parsing (Does not use AI or count against limits)
    Route::post('/semantic-analyze', [AnalyzerController::class, 'semanticAnalyze'])
        ->name('api.semantic');

    // 2. Google PageSpeed Insights Proxy (Counts against its own limit)
    Route::post('/semantic-analyzer/psi', [AnalyzerController::class, 'psiProxy'])
        ->name('api.psi');

    // 3. NEW Unified AI Request Handler (For all new AI features)
    // This single endpoint handles 'brief', 'suggestions', 'competitor', 'trends', etc.
    Route::post('/openai-request', [AnalyzerController::class, 'handleOpenAiRequest'])
        ->name('api.openai');

    // --- DEPRECATED BUT SUPPORTED ROUTES ---
    // These routes are kept for backward compatibility with older frontend versions.
    // They now internally call the new `handleOpenAiRequest` method.
    
    // Technical SEO analysis
    Route::post('/technical-seo-analyze', [AnalyzerController::class, 'technicalSeoAnalyze'])
        ->name('api.technical-seo');
        
    // Keyword Intelligence analysis
    Route::post('/keyword-analyze', [AnalyzerController::class, 'keywordAnalyze'])
        ->name('api.keyword-analyze');

    // Content Analysis Engine
    Route::post('/content-engine-analyze', [AnalyzerController::class, 'contentEngineAnalyze'])
        ->name('api.content-engine');
});

// A catch-all for CORS preflight requests to ensure smooth frontend integration
Route::options('/{any}', fn () => response()->noContent(204))->where('any', '.*');

