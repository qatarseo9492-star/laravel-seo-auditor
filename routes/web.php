<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SeoAuditController;
use App\Http\Controllers\KeywordAnalysisController;
use App\Http\Controllers\SeoOptimizerController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you register web routes for your application.
| These routes are loaded by the RouteServiceProvider and all of them
| will be assigned to the "web" middleware group. Make something great!
|
*/

// ✅ Homepage
Route::get('/', function () {
    return view('home'); // resources/views/home.blade.php
})->name('home');

// ✅ SEO Auditor Routes
Route::get('/seo-audit', [SeoAuditController::class, 'index'])->name('seo.audit.index');
Route::post('/seo-audit/analyze', [SeoAuditController::class, 'analyze'])->name('seo.audit.analyze');

// ✅ Keyword Analyzer Routes
Route::get('/seo-keyword-analysis', [KeywordAnalysisController::class, 'index'])->name('seo.keyword.index');
Route::post('/seo-keyword-analysis/analyze', [KeywordAnalysisController::class, 'analyze'])->name('seo.keyword.analyze');

// ✅ Content Optimizer Routes
Route::get('/seo-optimizer', [SeoOptimizerController::class, 'index'])->name('seo.optimizer.index');
Route::post('/seo-optimizer/analyze', [SeoOptimizerController::class, 'analyze'])->name('seo.optimizer.analyze');
