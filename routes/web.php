<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AnalyzerController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\CompareController;
use App\Http\Controllers\ToolsController;

Route::get('/', fn() => view('home'))->name('home');

/** Analyzer */
Route::post('/analyze-json', [AnalyzerController::class, 'analyze'])->name('analyze.json');

/** Reports */
Route::post('/report/pdf', [ReportController::class, 'pdf'])->name('report.pdf');
Route::post('/export/csv', [ReportController::class, 'csv'])->name('export.csv');
Route::post('/share/save', [ReportController::class, 'shareSave'])->name('share.save');
Route::get('/share/{id}', [ReportController::class, 'shareView'])->name('share.view');

/** Compare / Tools */
Route::post('/compare', [CompareController::class, 'compare'])->name('compare.run');
Route::post('/crawl/sitemap', [ToolsController::class, 'crawlSitemap'])->name('crawl.sitemap');
Route::post('/psi', [ToolsController::class, 'pageSpeed'])->name('psi.run');
Route::post('/schema/generate', [ToolsController::class, 'schemaGenerate'])->name('schema.generate');
Route::post('/social/preview', [ToolsController::class, 'socialPreview'])->name('social.preview');
Route::post('/entities/gap', [ToolsController::class, 'entitiesGap'])->name('entities.gap');
Route::post('/readability', [ToolsController::class, 'readability'])->name('readability.run');
Route::post('/hreflang/audit', [ToolsController::class, 'hreflang'])->name('hreflang.audit');
