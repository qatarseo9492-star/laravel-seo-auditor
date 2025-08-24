<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AnalyzeController;
use App\Http\Controllers\PsiController;
use App\Http\Controllers\CruxController;

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
 * Performance APIs (keys are never exposed to the browser)
 * /api/crux  -> Chrome UX Report (fast CWV p75)
 * /api/psi   -> PageSpeed Insights (full Lighthouse)
 */
Route::middleware('throttle:60,1')->get('/api/crux', [CruxController::class, 'url'])->name('crux.url');
Route::middleware('throttle:30,1')->get('/api/psi',  [PsiController::class, 'run'])->name('psi.run');

/* Optional quick health check */
Route::get('/ping', fn() => response()->json(['ok' => true, 'time' => now()->toIso8601String()]));
