<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Seo\TopicAnalysisController;
use App\Models\TopicAnalysis;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Home -> resources/views/home.blade.php
Route::view('/', 'home')->name('home');

/*
|--------------------------------------------------------------------------
| Auth Routes (custom controller)
| Views expected:
| - resources/views/auth/login.blade.php
| - resources/views/auth/register.blade.php
|--------------------------------------------------------------------------
*/
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Optional: forgot/reset flow (uncomment when implemented)
// Route::view('/forgot-password', 'auth.forgot')->name('password.request');

/*
|--------------------------------------------------------------------------
| Topic Cluster Identification & Mapping (protected)
| Form + processing + results. Guests redirect to login.
| Views expected:
| - resources/views/seo/topic-analysis.blade.php
| - resources/views/seo/topic-analysis-results.blade.php
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    // Form
    Route::get('/seo/topic-clusters', [TopicAnalysisController::class, 'create'])
        ->name('seo.topic-clusters.create');

    // Process submission
    Route::post('/seo/topic-clusters', [TopicAnalysisController::class, 'store'])
        ->name('seo.topic-clusters.store');

    // Results (route model binding)
    Route::get('/seo/topic-clusters/results/{analysis}', function (TopicAnalysis $analysis) {
        return view('seo.topic-analysis-results', ['analysis' => $analysis]);
    })->name('seo.topic-clusters.results');
});
