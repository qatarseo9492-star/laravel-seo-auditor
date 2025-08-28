<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
| These serve your Blade views. Keep them if you don't already have
| page routes defined elsewhere.
| Views used below:
| - resources/views/home.blade.php
| - resources/views/analyzers/semantic.blade.php
| - resources/views/analyzers/ai.blade.php
| - resources/views/analyzers/topic.blade.php
*/

Route::view('/', 'home')->name('home');

Route::prefix('/')->group(function () {
    Route::view('semantic-analyzer', 'analyzers.semantic')->name('semantic');
    Route::view('ai-content-checker', 'analyzers.ai')->name('aiChecker');
    Route::view('topic-cluster', 'analyzers.topic')->name('topicCluster');
});

/*
| Optional placeholders for auth pages if you haven’t installed auth scaffolding.
| Remove these if your project already registers /login and /register.
*/
Route::view('/login', 'auth.login')->name('login');
Route::view('/register', 'auth.register')->name('register');

/*
| Uptime/health check (handy for Cloudways monitors)
*/
Route::get('/_up', fn () => response('OK', 200))->name('_up');

/*
| Optional: fallback to 404 view if you have resources/views/errors/404.blade.php
| Uncomment if desired.
*/
// Route::fallback(function () {
//     if (view()->exists('errors.404')) {
//         return response()->view('errors.404', [], 404);
//     }
//     abort(404);
// });
