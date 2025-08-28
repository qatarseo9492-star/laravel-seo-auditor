<?php

use Illuminate\Support\Facades\Route;
// Alias the existing AnalyzeController to the name used in routes
use App\Http\Controllers\AnalyzeController as AnalyzerController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
| These power the JS fetch() calls in your Blade pages.
| All routes here are automatically prefixed with /api
| e.g. POST /api/semantic-analyze
*/

// Simple health check
Route::get('/status', function () {
    return response()->json([
        'ok'      => true,
        'service' => 'Semantic SEO Master Analyzer API',
        'version' => '1.0',
        'time'    => now()->toIso8601String(),
    ]);
})->name('api.status');

// Main analyzer endpoints
Route::middleware('throttle:60,1')->group(function () {
    Route::post('/semantic-analyze', [AnalyzerController::class, 'semanticAnalyze'])->name('api.semantic');
    Route::post('/ai-check',         [AnalyzerController::class, 'aiCheck'])->name('api.aicheck');
    Route::post('/topic-cluster',    [AnalyzerController::class, 'topicClusterAnalyze'])->name('api.topiccluster');
});

// (Optional) Allow CORS preflight for any route under /api
Route::options('/{any}', function () {
    return response()->noContent(204);
})->where('any', '.*');
