<?php

use Illuminate\Support\Facades\Route;
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
