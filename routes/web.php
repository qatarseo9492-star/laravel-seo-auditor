<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AnalyzerController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\UserAdminController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Homepage
Route::view('/', 'home')->name('home');

// Authentication Routes
Route::controller(AuthController::class)->group(function () {
    Route::get('/login', 'showLogin')->name('login');
    Route::post('/login', 'login')->name('login.post');
    Route::get('/register', 'showRegister')->name('register');
    Route::post('/register', 'register')->name('register.post');
    Route::post('/logout', 'logout')->name('logout');
});

// Authenticated User Routes
Route::middleware(['auth', 'ban', 'presence'])->group(function () {
    Route::view('/dashboard', 'dashboard')->name('dashboard');

    // Analyzer UI Pages
    Route::view('/semantic-analyzer', 'analyzers.semantic')->name('semantic.analyzer');
    Route::view('/ai-content-checker', 'analyzers.ai')->name('ai.checker');
    Route::view('/topic-cluster', 'analyzers.topic')->name('topic.cluster');

    // Profile Management
    Route::controller(ProfileController::class)->prefix('profile')->name('profile.')->group(function () {
        Route::get('/', 'edit')->name('edit');
        Route::post('/update', 'updateProfile')->name('update');
        Route::post('/password', 'updatePassword')->name('password');
        Route::post('/avatar', 'updateAvatar')->name('avatar');
    });

    /*
    |--------------------------------------------------------------------------
    | Internal API Routes for the Analyzer
    |--------------------------------------------------------------------------
    | ✅ MOVED: These routes now use the 'web' middleware group, giving them
    | access to session state, CSRF protection, and the authenticated user.
    | This is crucial for logging and enforcing user-specific quotas.
    */
    Route::prefix('api')->middleware('quota')->group(function() {
        // Main content analysis
        Route::post('/semantic-analyze', [AnalyzerController::class, 'analyze'])->name('api.semantic');

        // Technical SEO analysis
        Route::post('/technical-seo-analyze', [AnalyzerController::class, 'analyzeTechnicalSeo'])->name('api.technical-seo');
        
        // Keyword Intelligence analysis
        Route::post('/keyword-analyze', [AnalyzerController::class, 'analyzeKeywords'])->name('api.keyword-analyze');

        // Content Analysis Engine
        Route::post('/content-engine-analyze', [AnalyzerController::class, 'analyzeContentEngine'])->name('api.content-engine');

        // Optional stubs (safe to keep)
        Route::post('/ai-check', [AnalyzerController::class, 'aiCheck'])->name('api.aicheck');
        Route::post('/topic-cluster', [AnalyzerController::class, 'topicClusterAnalyze'])->name('api.topiccluster');
    });

    // PageSpeed Insights Proxy
    Route::post('/semantic-analyzer/psi', [AnalyzerController::class, 'psiProxy'])
        ->name('semantic.psi')
        ->middleware('quota');
});

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'ban', 'presence', 'admin'])
    ->prefix('admin')->name('admin.')
    ->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::patch('/users/{user}/limit', [UserAdminController::class, 'updateUserLimit'])->name('users.limit');
        Route::patch('/users/{user}/ban', [UserAdminController::class, 'toggleBan'])->name('users.ban');
    });

/*
|--------------------------------------------------------------------------
| Utility & Fallback Routes
|--------------------------------------------------------------------------
*/
Route::get('/_up', fn () => response('OK', 200))->name('_up');
Route::fallback(fn () => redirect()->route('home'));

