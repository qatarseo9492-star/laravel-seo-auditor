<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AnalyzerController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\UserAdminController;
use App\Http\Controllers\Admin\UserLimitsController;

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

    // PageSpeed Insights Proxy
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

        // Admin dashboard page
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        // --- Live JSON feeds used by the upgraded dashboard (poll every 10s)
        Route::get('/dashboard/live', [DashboardController::class, 'live'])->name('dashboard.live');
        Route::get('/users/{user}/live', [DashboardController::class, 'userLive'])->name('users.live');

        // --- User actions (existing + new limits endpoint)
        Route::patch('/users/{user}/limit', [UserAdminController::class, 'updateUserLimit'])->name('users.limit');
        Route::patch('/users/{user}/ban', [UserAdminController::class, 'toggleBan'])->name('users.ban');
        Route::patch('/users/{user}/limits', [UserLimitsController::class, 'update'])->name('users.updateLimits');

        // --- Optional: preview of the new dashboard view (safe/read-only)
        Route::get('/dashboard-v3', function () {
            $view = \Illuminate\Support\Facades\View::exists('admin.dashboard-v3') ? 'admin.dashboard-v3' : 'admin.dashboard';
            return view($view, [
                'kpis'          => [],
                'traffic'       => [],
                'services'      => [],
                'topQueries'    => [],
                'errors'        => [],
                'limitsSummary' => [],
                'health'        => [],
            ]);
        })->name('dashboard.v3');
    });

/*
|--------------------------------------------------------------------------
| Utility & Fallback Routes
|--------------------------------------------------------------------------
*/
Route::get('/_up', fn () => response('OK', 200))->name('_up');
Route::fallback(fn () => redirect()->route('home'));
