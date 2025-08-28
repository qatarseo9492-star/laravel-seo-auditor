<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * Where to redirect users after login/registration.
     * (Used by some auth scaffolds; safe default for this app.)
     */
    public const HOME = '/dashboard';

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->configureRateLimiting();

        $this->routes(function () {
            // API routes (stateless)
            Route::prefix('api')
                ->middleware('api')
                ->group(base_path('routes/api.php'));

            // Web routes (sessions, cookies, CSRF, auth)
            Route::middleware('web')
                ->group(base_path('routes/web.php'));
        });
    }

    /**
     * Configure rate limiters.
     * - Default `api` limiter (60/min)
     * - Custom `seoapi` limiter (240/min), keyed by user|IP (after TrustProxies)
     */
    protected function configureRateLimiting(): void
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by(
                $request->user()?->id ?? $request->ip()
            );
        });

        RateLimiter::for('seoapi', function (Request $request) {
            $key = ($request->user()?->id ?? 'guest') . '|' . $request->ip();
            return Limit::perMinute(240)->by($key);
        });
    }
}
