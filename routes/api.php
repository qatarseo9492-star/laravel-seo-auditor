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
        'version' => '3.0', // Final Version
        'time'    => now()->toIso8601String(),
    ]);
})->name('api.status');

// Group all analysis endpoints under a throttle middleware to prevent abuse
Route::middleware('throttle:seoapi')->group(function () {
    
    // 1. Initial Local HTML Parsing -> Maps to handleLocalAnalysis()
    Route::post('/semantic-analyze', [AnalyzerController::class, 'handleLocalAnalysis'])
        ->name('api.semantic');

    // 2. Google PageSpeed Insights Proxy -> Maps to psiProxy()
    Route::post('/semantic-analyzer/psi', [AnalyzerController::class, 'psiProxy'])
        ->name('api.psi');

    // 3. Unified AI Request Handler (For new AI features) -> Maps to handleOpenAiRequest()
    Route::post('/openai-request', [AnalyzerController::class, 'handleOpenAiRequest'])
        ->name('api.openai');

    // --- Legacy Routes for older frontend compatibility ---
    
    // Technical SEO analysis -> Maps to technicalSeoAnalyze()
    Route::post('/technical-seo-analyze', [AnalyzerController::class, 'technicalSeoAnalyze'])
        ->name('api.technical-seo');
        
    // Keyword Intelligence analysis -> Maps to keywordAnalyze()
    Route::post('/keyword-analyze', [AnalyzerController::class, 'keywordAnalyze'])
        ->name('api.keyword-analyze');

    // Content Analysis Engine -> Maps to contentEngineAnalyze()
    Route::post('/content-engine-analyze', [AnalyzerController::class, 'contentEngineAnalyze'])
        ->name('api.content-engine');
});

// A catch-all for CORS preflight requests to ensure smooth frontend integration
Route::options('/{any}', fn () => response()->noContent(204))->where('any', '.*');
