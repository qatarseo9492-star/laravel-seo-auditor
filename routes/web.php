<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;   // <-- added
use Illuminate\Http\Request;          // <-- added
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AnalyzerController; // <-- existing

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
| - resources/views/profile/edit.blade.php
| - resources/views/auth/login.blade.php
| - resources/views/auth/register.blade.php
*/

/*
| Public homepage (marketing-only)
*/
Route::view('/', 'home')->name('home');

/*
| Auth pages
| GET shows forms; POST processes them.
*/
Route::get('/login',  [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');

Route::get('/register',  [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.post');

/*
| Auth-protected app
*/
Route::middleware('auth')->group(function () {
    Route::view('/dashboard', 'dashboard')->name('dashboard');

    // UI pages
    Route::view('/semantic-analyzer', 'analyzers.semantic')->name('semantic.analyzer');
    Route::view('/ai-content-checker', 'analyzers.ai')->name('ai.checker');
    Route::view('/topic-cluster', 'analyzers.topic')->name('topic.cluster');

    // Analyzer JSON endpoint (used by the Blade fetch() call)
    Route::post('/semantic-analyzer/analyze', [AnalyzerController::class, 'semanticAnalyze'])
        ->name('semantic.analyze'); // <-- existing

    // ===== PageSpeed Insights proxy (NEW) =====
    // Keeps PSI key server-side; frontend calls this with { url }
    Route::post('/semantic-analyzer/psi', function (Request $req) {
        $url = (string) $req->input('url');
        abort_unless(filter_var($url, FILTER_VALIDATE_URL), 422, 'Invalid URL');

        $key = config('services.psi.key', env('GOOGLE_PSI_KEY'));
        abort_unless($key, 500, 'PSI key missing');

        $base = 'https://www.googleapis.com/pagespeedonline/v5/runPagespeed';

        $fetch = function (string $strategy) use ($base, $url, $key) {
            $res = Http::retry(2, 200)
                ->acceptJson()
                ->get($base, [
                    'url'       => $url,
                    'strategy'  => $strategy,           // 'mobile' | 'desktop'
                    'category'  => 'performance',
                    'key'       => $key,
                ]);

            if (!$res->ok()) {
                return [
                    'error' => [
                        'status' => $res->status(),
                        'body'   => $res->body(),
                    ]
                ];
            }
            return $res->json();
        };

        return response()->json([
            'mobile'  => $fetch('mobile'),
            'desktop' => $fetch('desktop'),
        ]);
    })->name('semantic.psi');
    // ==========================================

    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::post('/profile', [ProfileController::class, 'updateProfile'])->name('profile.update');
    Route::post('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');
    Route::post('/profile/avatar', [ProfileController::class, 'updateAvatar'])->name('profile.avatar');

    // Logout (POST)
    Route::post('/logout', function () {
        Auth::logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();
        return redirect()->route('home');
    })->name('logout');
});

/*
| Convenience redirects / legacy aliases
*/
Route::get('/semantic', fn () => redirect()->route(auth()->check() ? 'semantic.analyzer' : 'login'))->name('semantic');
Route::get('/ai-checker', fn () => redirect()->route(auth()->check() ? 'ai.checker' : 'login'))->name('aiChecker');
Route::get('/topic', fn () => redirect()->route(auth()->check() ? 'topic.cluster' : 'login'))->name('topicCluster');

/*
| Uptime
*/
Route::get('/_up', fn () => response('OK', 200))->name('_up');

/*
| Fallback
*/
Route::fallback(fn () => redirect()->route('home'));
