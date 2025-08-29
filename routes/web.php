<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;   // <-- added
use Illuminate\Http\Request;

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AnalyzerController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/
Route::view('/', 'home')->name('home');

/*
| Auth pages
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

    // Analyzer JSON endpoint
    Route::post('/semantic-analyzer/analyze', [AnalyzerController::class, 'semanticAnalyze'])
        ->name('semantic.analyze');

    // ===== PageSpeed Insights proxy (FIXED) =====
    Route::post('/semantic-analyzer/psi', function (Request $req) {
        $url = trim((string) $req->input('url'));
        abort_unless(filter_var($url, FILTER_VALIDATE_URL), 422, 'Invalid URL');

        // Read from services.pagespeed.* (matches your config/services.php)
        $cfg      = config('services.pagespeed', []);
        $key      = $cfg['key']       ?? null;  // <-- fixed
        $endpoint = $cfg['endpoint']  ?? 'https://www.googleapis.com/pagespeedonline/v5/runPagespeed';
        $timeout  = (int)($cfg['timeout']   ?? 20);
        $ttl      = (int)($cfg['cache_ttl'] ?? 60);

        abort_unless($key, 500, 'PSI key missing');

        $fetch = function (string $strategy) use ($endpoint, $url, $key, $timeout, $ttl) {
            $cacheKey = "psi:" . $strategy . ":" . md5($url);
            return Cache::remember($cacheKey, $ttl, function () use ($endpoint, $url, $key, $timeout, $strategy) {

                $res = Http::timeout($timeout)
                    ->retry(2, 250)
                    ->acceptJson()
                    ->get($endpoint, [
                        'url'      => $url,
                        'strategy' => $strategy,     // 'mobile' | 'desktop'
                        'category' => 'performance',
                        'key'      => $key,
                    ]);

                if (!$res->ok()) {
                    return [
                        '_error' => [
                            'status' => $res->status(),
                            'body'   => $res->body(),
                        ],
                    ];
                }

                $j        = $res->json();
                $lr       = $j['lighthouseResult'] ?? [];
                $audits   = $lr['audits'] ?? [];
                $perfRaw  = $lr['categories']['performance']['score'] ?? null;
                $score    = is_null($perfRaw) ? null : round($perfRaw * 100);

                $num = function (string $id) use ($audits) {
                    return $audits[$id]['numericValue'] ?? null;
                };

                return [
                    'score'  => $score,
                    'lcp'    => $num('largest-contentful-paint'),           // seconds
                    'cls'    => $audits['cumulative-layout-shift']['numericValue'] ?? null,
                    'inp'    => $num('interaction-to-next-paint'),           // ms
                    'ttfb'   => $num('server-response-time'),                // ms
                    'raw'    => $j,                                          // keep full JSON if UI needs more
                ];
            });
        };

        return response()->json([
            'mobile'  => $fetch('mobile'),
            'desktop' => $fetch('desktop'),
        ]);
    })->name('semantic.psi');
    // ===========================================

    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::post('/profile', [ProfileController::class, 'updateProfile'])->name('profile.update');
    Route::post('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');
    Route::post('/profile/avatar', [ProfileController::class, 'updateAvatar'])->name('profile.avatar');

    // Logout
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
