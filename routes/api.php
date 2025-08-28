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
    Route::post('/semantic-analyze', [AnalyzerController::class, 'semanticAnalyze'])->name('api.semantic');

    // Optional stubs (safe to keep)
    Route::post('/ai-check', [AnalyzerController::class, 'aiCheck'])->name('api.aicheck')
        ->withoutMiddleware('throttle:seoapi'); // remove if you added the stub methods
    Route::post('/topic-cluster', [AnalyzerController::class, 'topicClusterAnalyze'])->name('api.topiccluster')
        ->withoutMiddleware('throttle:seoapi'); // remove if you added the stub methods
});

// CORS preflight
Route::options('/{any}', fn () => response()->noContent(204))->where('any', '.*');
