<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AnalyzeController;
use App\Http\Controllers\PsiController; // <-- add this

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
 * PageSpeed Insights proxy (keeps API key server-side)
 * Example: GET /api/psi?url=https://example.com&strategy=mobile
 * strategy: mobile|desktop (default mobile)
 */
Route::middleware('throttle:30,1')->get('/api/psi', [PsiController::class, 'run'])->name('psi.run');

/* Optional quick health check */
Route::get('/ping', fn() => response()->json(['ok' => true, 'time' => now()->toIso8601String()]));
