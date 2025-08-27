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

// Authentication (custom lightweight)
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Auth-required area
Route::middleware('auth')->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Account
    Route::get('/account', [AccountController::class, 'show'])->name('account.show');
    Route::put('/account/profile', [AccountController::class, 'updateProfile'])->name('account.update.profile');
    Route::put('/account/password', [AccountController::class, 'updatePassword'])->name('account.update.password');
    Route::post('/account/avatar', [AccountController::class, 'updateAvatar'])->name('account.update.avatar');

    // Topic Cluster Identification & Mapping
    Route::get('/seo/topic-clusters', [TopicAnalysisController::class, 'create'])->name('seo.topic-clusters.create');
    Route::post('/seo/topic-clusters', [TopicAnalysisController::class, 'store'])->name('seo.topic-clusters.store');
    Route::get('/seo/topic-clusters/results/{analysis}', function (TopicAnalysis $analysis) {
        return view('seo.topic-analysis-results', ['analysis' => $analysis]);
    })->name('seo.topic-clusters.results');

    // Analyze endpoints (both paths provided to avoid conflicts with servers blocking /api/*)
    Route::post('/ajax/analyze-url', AnalyzeProxyController::class)->name('ajax.analyze.url');
    Route::post('/api/analyze-url', AnalyzeProxyController::class)->name('api.analyze.url');

    // Quick ping to verify routing works
    Route::get('/ajax/ping', fn() => response()->json(['ok' => true, 'route' => '/ajax/ping']))->name('ajax.ping');
});

// Optional fallback
// Route::fallback(fn() => redirect()->route('home'));
