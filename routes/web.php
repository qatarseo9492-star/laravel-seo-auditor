<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Seo\TopicAnalysisController;
use App\Http\Controllers\Seo\AnalyzeProxyController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AccountController;
use App\Models\TopicAnalysis;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Home
Route::view('/', 'home')->name('home');

/*
|--------------------------------------------------------------------------
| Authentication (custom lightweight)
| Views expected:
|  - resources/views/auth/login.blade.php
|  - resources/views/auth/register.blade.php
|--------------------------------------------------------------------------
*/
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

/*
|--------------------------------------------------------------------------
| Auth-required area
|--------------------------------------------------------------------------
| Users must be logged in to access tools, dashboard and account settings.
*/
Route::middleware('auth')->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Account management
    Route::get('/account', [AccountController::class, 'show'])->name('account.show');
    Route::put('/account/profile', [AccountController::class, 'updateProfile'])->name('account.update.profile');
    Route::put('/account/password', [AccountController::class, 'updatePassword'])->name('account.update.password');
    Route::post('/account/avatar', [AccountController::class, 'updateAvatar'])->name('account.update.avatar');

    // Topic Cluster Identification & Mapping
    Route::get('/seo/topic-clusters', [TopicAnalysisController::class, 'create'])
        ->name('seo.topic-clusters.create');
    Route::post('/seo/topic-clusters', [TopicAnalysisController::class, 'store'])
        ->name('seo.topic-clusters.store');
    Route::get('/seo/topic-clusters/results/{analysis}', function (TopicAnalysis $analysis) {
        return view('seo.topic-analysis-results', ['analysis' => $analysis]);
    })->name('seo.topic-clusters.results');

    // Analyze proxy endpoint (used by Analyze button on home page)
    Route::post('/api/analyze-url', AnalyzeProxyController::class)->name('api.analyze.url');
});

/*
|--------------------------------------------------------------------------
| (Optional) Fallback to home or a pretty 404
|--------------------------------------------------------------------------
*/
// Route::fallback(fn() => redirect()->route('home'));
