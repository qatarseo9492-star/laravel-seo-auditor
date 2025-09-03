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
        'version' => '5.0', // Final Corrected Version
        'time'    => now()->toIso8601String(),
    ]);
})->name('api.status');

// Group all analysis endpoints under a throttle middleware
Route::middleware('throttle:seoapi')->group(function () {
    
    // 1. Initial Local HTML Parsing -> CORRECTED to semanticAnalyze()
    Route::post('/semantic-analyze', [AnalyzerController::class, 'semanticAnalyze'])
        ->name('api.semantic');

    // 2. Unified AI Request Handler
    Route::post('/openai-request', [AnalyzerController::class, 'handleOpenAiRequest'])
        ->name('api.openai');

    // --- Legacy Routes for older frontend compatibility ---
    
    Route::post('/technical-seo-analyze', [AnalyzerController::class, 'technicalSeoAnalyze'])
        ->name('api.technical-seo');
        
    Route::post('/keyword-analyze', [AnalyzerController::class, 'keywordAnalyze'])
        ->name('api.keyword-analyze');

    Route::post('/content-engine-analyze', [AnalyzerController::class, 'contentEngineAnalyze'])
        ->name('api.content-engine');
});

// A catch-all for CORS preflight requests
Route::options('/{any}', fn () => response()->noContent(204))->where('any', '.*');

