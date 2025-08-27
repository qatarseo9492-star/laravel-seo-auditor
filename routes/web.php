<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Seo\TopicAnalysisController;
use App\Models\TopicAnalysis;

// Home -> resources/views/home.blade.php
Route::view('/', 'home')->name('home');

// Topic Cluster Analyzer (GET form + POST process)
Route::get('/seo/topic-clusters', [TopicAnalysisController::class, 'create'])
    ->name('seo.topic-clusters.create');

Route::post('/seo/topic-clusters', [TopicAnalysisController::class, 'store'])
    ->name('seo.topic-clusters.store');

// Results page
Route::get('/seo/topic-clusters/results/{analysis}', function (TopicAnalysis $analysis) {
    return view('seo.topic-analysis-results', ['analysis' => $analysis]);
})->name('seo.topic-clusters.results');
