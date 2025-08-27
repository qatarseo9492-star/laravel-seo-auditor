<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Seo\TopicAnalysisController;
use App\Models\TopicAnalysis;

// Home
Route::view('/', 'home')->name('home');

// Public auth routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Tools (protected)
Route::middleware('auth')->group(function () {
    // Topic Cluster Analyzer
    Route::get('/seo/topic-clusters', [TopicAnalysisController::class, 'create'])
        ->name('seo.topic-clusters.create');
    Route::post('/seo/topic-clusters', [TopicAnalysisController::class, 'store'])
        ->name('seo.topic-clusters.store');

    // Results page
    Route::get('/seo/topic-clusters/results/{analysis}', function (TopicAnalysis $analysis) {
        return view('seo.topic-analysis-results', ['analysis' => $analysis]);
    })->name('seo.topic-clusters.results');

    // Future tools (examples, add later)
    // Route::get('/seo/keyword-mapper', ...)   ->name('seo.keyword-mapper');
    // Route::get('/seo/entity-extractor', ...) ->name('seo.entity-extractor');
});
