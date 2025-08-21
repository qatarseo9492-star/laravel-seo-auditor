<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SeoAuditController;
use App\Http\Controllers\KeywordAnalysisController;
use App\Http\Controllers\ContentOptimizerController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application.
| These routes are loaded by the RouteServiceProvider within a group 
| which contains the "web" middleware group. Now create something great!
|
*/

// ✅ Homepage
Route::get('/', function () {
    return view('home');
});

// ✅ SEO Auditor
Route::get('/seo-audit', [SeoAuditController::class, 'index'])->name('seo.audit');
Route::post('/seo-audit/analyze', [SeoAuditController::class, 'analyze'])->name('seo.audit.analyze');

// ✅ Keyword Analyzer
Route::get('/seo-keyword-analysis', [KeywordAnalysisController::class, 'index'])->name('seo.keyword');
Route::post('/seo-keyword-analysis/analyze', [KeywordAnalysisController::class, 'analyze'])->name('seo.keyword.analyze');

// ✅ Content Optimizer
Route::get('/seo-optimizer', [ContentOptimizerController::class, 'index'])->name('seo.optimizer');
Route::post('/seo-optimizer/analyze', [ContentOptimizerController::class, 'analyze'])->name('seo.optimizer.analyze');
