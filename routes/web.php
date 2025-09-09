<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AnalyzerController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\UserAdminController;
use App\Http\Controllers\Admin\UserLimitsController; // kept in case you still use it elsewhere

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| These routes are loaded by the RouteServiceProvider within the "web"
| middleware group.
|
*/

// Homepage
Route::view('/', 'home')->name('home');

// Authentication
Route::controller(AuthController::class)->group(function () {
    Route::get('/login', 'showLogin')->name('login');
    Route::post('/login', 'login')->name('login.post');
    Route::get('/register', 'showRegister')->name('register');
    Route::post('/register', 'register')->name('register.post');
    Route::post('/logout', 'logout')->name('logout');
});

// Authenticated (non-admin) app
Route::middleware(['auth', 'ban', 'presence'])->group(function () {
    Route::view('/dashboard', 'dashboard')->name('dashboard');

    // Analyzer UI pages
    Route::view('/semantic-analyzer', 'analyzers.semantic')->name('semantic.analyzer');
    Route::view('/ai-content-checker', 'analyzers.ai')->name('ai.checker');
    Route::view('/topic-cluster', 'analyzers.topic')->name('topic.cluster');

    // Profile
    Route::prefix('profile')->name('profile.')->controller(ProfileController::class)->group(function () {
        Route::get('/', 'edit')->name('edit');
        Route::post('/update', 'updateProfile')->name('update');
        Route::post('/password', 'updatePassword')->name('password');
        Route::post('/avatar', 'updateAvatar')->name('avatar');
    });

    // PageSpeed Insights proxy
    Route::post('/semantic-analyzer/psi', [AnalyzerController::class, 'pageSpeedInsights'])
        ->name('semantic.psi')
        ->middleware('throttle:seoapi');
});

/*
|--------------------------------------------------------------------------
| Admin
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'ban', 'presence', 'admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        // Main Admin Dashboard
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::get('/dashboard/live', [DashboardController::class, 'live'])->name('dashboard.live');

        // Users — Live (page + JSON feeds)
        // Name is 'admin.users' so {{ route('admin.users') }} works
        Route::get('/users', [UserAdminController::class, 'index'])->name('users');

        // Data feeds used by the Users page
        Route::get('/users/table', [UserAdminController::class, 'table'])->name('users.table');
        Route::get('/users/{user}/live', [UserAdminController::class, 'live'])->name('users.live');
        Route::get('/users/{user}/sessions', [UserAdminController::class, 'sessions'])->name('users.sessions');

        // Actions
        Route::patch('/users/{user}/ban', [UserAdminController::class, 'toggleBan'])->name('users.ban');
        Route::patch('/users/{user}/upgrade', [UserAdminController::class, 'upgrade'])->name('users.upgrade');

        // Limits (keep both: legacy + new)
        Route::patch('/users/{user}/limit', [UserAdminController::class, 'updateUserLimit'])->name('users.limit');   // legacy
        Route::patch('/users/{user}/limits', [UserAdminController::class, 'updateUserLimit'])->name('users.limits'); // new UI

        // Optional: safe preview route (kept)
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
| Utility & Fallback
|--------------------------------------------------------------------------
*/
Route::get('/_up', fn () => response('OK', 200))->name('_up');
Route::fallback(fn () => redirect()->route('home'));
