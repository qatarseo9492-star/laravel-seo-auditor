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
|
| These routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group.
|
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

    // PageSpeed Insights Proxy - Corrected to use pageSpeedInsights method
    Route::post('/semantic-analyzer/psi', [AnalyzerController::class, 'pageSpeedInsights'])
        ->name('semantic.psi')
        ->middleware('throttle:seoapi');
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

