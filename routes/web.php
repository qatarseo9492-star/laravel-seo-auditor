<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
| Blade views used below:
| - resources/views/home.blade.php
| - resources/views/dashboard.blade.php
| - resources/views/analyzers/semantic.blade.php
| - resources/views/analyzers/ai.blade.php
| - resources/views/analyzers/topic.blade.php
| - resources/views/auth/login.blade.php      (placeholder if you don't use Breeze/Jetstream)
| - resources/views/auth/register.blade.php   (placeholder if you don't use Breeze/Jetstream)
*/

/*
| Public homepage (marketing-only; tools not exposed here)
*/
Route::view('/', 'home')->name('home');

/*
| Auth-protected app pages
| Anyone hitting these while not logged in will be redirected to route('login').
*/
Route::middleware('auth')->group(function () {
    // Dashboard (fixes 404 for /dashboard)
    Route::view('/dashboard', 'dashboard')->name('dashboard');

    // Tool shells (views should exist)
    Route::view('/semantic-analyzer', 'analyzers.semantic')->name('semantic.analyzer');
    Route::view('/ai-content-checker', 'analyzers.ai')->name('ai.checker');
    Route::view('/topic-cluster', 'analyzers.topic')->name('topic.cluster');
});

/*
| Optional placeholders if you don’t have auth scaffolding installed.
| Remove these two lines if Breeze/Jetstream or your own auth already registers /login and /register.
*/
Route::view('/login', 'auth.login')->name('login');
Route::view('/register', 'auth.register')->name('register');

/*
| Convenience redirects / legacy aliases (keeps old links working)
| These detect auth and send users to the right place (or to login).
*/
Route::get('/semantic', function () {
    return redirect()->route(auth()->check() ? 'semantic.analyzer' : 'login');
})->name('semantic');

Route::get('/ai-checker', function () {
    return redirect()->route(auth()->check() ? 'ai.checker' : 'login');
})->name('aiChecker');

Route::get('/topic', function () {
    return redirect()->route(auth()->check() ? 'topic.cluster' : 'login');
})->name('topicCluster');

/*
| Uptime/health check (handy for Cloudways monitors)
*/
Route::get('/_up', fn () => response('OK', 200))->name('_up');

/*
| Fallback: redirect unknown pages to home (or swap for a 404 view if you prefer)
*/
Route::fallback(function () {
    return redirect()->route('home');
    // If you have resources/views/errors/404.blade.php and prefer a 404:
    // return response()->view('errors.404', [], 404);
});
