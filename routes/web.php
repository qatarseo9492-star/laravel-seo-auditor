<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SeoAuditController;  // your existing auditor controller
use App\Http\Controllers\SeoKeywordController; // if you made keyword tool before
use App\Http\Controllers\SeoOptimizerController; // new optimizer

// Existing route(s)
Route::get('/', function () {
    return view('home');
});

Route::get('/seo-audit', [SeoAuditController::class, 'audit'])->name('seo.audit');

// (Optional) If you kept the keyword analysis feature
Route::get('/seo-keyword-analysis', function() {
    return view('seo.analyze');
});
Route::post('/seo-keyword-analysis', [SeoKeywordController::class, 'analyze']);

// 🚀 New Feature: SEO Optimizer (URL + keyword)
Route::get('/seo-optimizer', [SeoOptimizerController::class, 'showForm']);
Route::post('/seo-optimizer/analyze', [SeoOptimizerController::class, 'analyze']);
