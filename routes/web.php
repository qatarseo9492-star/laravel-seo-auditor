<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Http\Request;

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AnalyzerController;
// 🔹 Admin dashboard controller
use App\Http\Controllers\Admin\DashboardController;

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
Route::middleware(['auth', 'ban', 'touch'])->group(function () { // 🔹 added ban + touch
    Route::view('/dashboard', 'dashboard')->name('dashboard');

    // UI pages
    Route::view('/semantic-analyzer', 'analyzers.semantic')->name('semantic.analyzer');
    Route::view('/ai-content-checker', 'analyzers.ai')->name('ai.checker');
    Route::view('/topic-cluster', 'analyzers.topic')->name('topic.cluster');

    // ===== Analyzer JSON endpoint (UPDATED) =====
    // Previously: -> 'semanticAnalyze'
    // Now points to the new OpenAI-powered controller method (CSRF-protected)
    Route::post('/semantic-analyzer/analyze', [AnalyzerController::class, 'analyzeWeb'])
        ->name('semantic.analyze')
        ->middleware('quota:semantic'); // 🔹 enforce daily/monthly quota for semantic

    // (Optional) Direct endpoint to call the API-style method from web if needed
    // Useful for testing the raw JSON without CSRF issues in certain setups
    Route::post('/semantic-analyzer/analyze-direct', [AnalyzerController::class, 'analyze'])
        ->name('semantic.analyze.direct')
        ->middleware('quota:semantic'); // 🔹 quota too

    // ===== PageSpeed Insights proxy (FINAL) =====
    Route::post('/semantic-analyzer/psi', function (Request $req) {
        $url = trim((string) $req->input('url'));
        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            return response()->json(['ok' => false, 'error' => 'Invalid URL'], 422);
        }

        // Read from services.pagespeed.* (matches config/services.php)
        $cfg      = config('services.pagespeed', []);
        $key      = $cfg['key']       ?? env('PAGESPEED_API_KEY') ?? env('GOOGLE_PSI_KEY');
        $endpoint = $cfg['endpoint']  ?? 'https://www.googleapis.com/pagespeedonline/v5/runPagespeed';
        $timeout  = (int)($cfg['timeout']   ?? 20);
        $ttl      = (int)($cfg['cache_ttl'] ?? 60);

        if (!$key) {
            // Friendly 200 so UI can render a helpful message
            return response()->json([
                'ok'    => false,
                'error' => 'PSI key missing',
                'hint'  => 'Set PAGESPEED_API_KEY in .env and run: php artisan config:clear',
            ], 200);
        }

        $fetch = function (string $strategy) use ($endpoint, $url, $key, $timeout, $ttl) {
            $strategy = strtolower($strategy) === 'desktop' ? 'desktop' : 'mobile';
            $cacheKey = "psi:{$strategy}:" . md5($url);

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
                        'ok'     => false,
                        'status' => $res->status(),
                        'error'  => 'HTTP ' . $res->status(),
                    ];
                }

                $j      = $res->json() ?: [];
                $lr     = $j['lighthouseResult'] ?? [];
                $audits = $lr['audits'] ?? [];

                // Performance score 0–1 -> 0–100
                $perfRaw = $lr['categories']['performance']['score'] ?? null;
                $score   = is_null($perfRaw) ? null : (int) round($perfRaw * 100);

                // Audits (with fallbacks)
                $lcp_ms  = $audits['largest-contentful-paint']['numericValue'] ?? null;
                $cls_val = $audits['cumulative-layout-shift']['numericValue'] ?? null;
                $inp_ms  = $audits['interaction-to-next-paint']['numericValue']
                    ?? ($audits['experimental-interaction-to-next-paint']['numericValue'] ?? null);
                $ttfb_ms = $audits['server-response-time']['numericValue']
                    ?? ($audits['time-to-first-byte']['numericValue'] ?? null);

                return [
                    'ok'       => true,
                    'strategy' => $strategy,
                    'score'    => $score,                                   // 0–100
                    'lcp'      => is_numeric($lcp_ms)  ? round($lcp_ms/1000, 2) : null, // seconds
                    'cls'      => is_numeric($cls_val) ? round($cls_val, 3)      : null, // unitless
                    'inp'      => is_numeric($inp_ms)  ? (int) round($inp_ms)    : null, // ms
                    'ttfb'     => is_numeric($ttfb_ms) ? (int) round($ttfb_ms)   : null, // ms
                ];
            });
        };

        return response()->json([
            'ok'      => true,
            'url'     => $url,
            'mobile'  => $fetch('mobile'),
            'desktop' => $fetch('desktop'),
        ]);
    })->name('semantic.psi')->middleware('quota:psi'); // 🔹 quota for PSI
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
| 🔹 Admin Dashboard (role-aware later)
*/
Route::middleware(['auth', 'ban', 'touch'])
    ->prefix('admin')->name('admin.')
    ->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])
            ->name('dashboard');
        // (We’ll add ban/unban, quota update, and data JSON endpoints next)
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
