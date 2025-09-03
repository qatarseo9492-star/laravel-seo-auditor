<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    public const HOME = '/dashboard';

    public function boot(): void
    {
        $this->configureRateLimiting();

        $this->routes(function () {
            Route::prefix('api')->middleware('api')->group(base_path('routes/api.php'));
            Route::middleware('web')->group(base_path('routes/web.php'));
        });
    }

    protected function configureRateLimiting(): void
    {
        // Default API limiter (unchanged)
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?? $request->ip());
        });

        // SEO analyzer limiter: 200 requests in a 24-hour rolling window per user (fallback to IP)
        RateLimiter::for('seoapi', function (Request $request) {
            $key = $request->user()?->id ?? $request->ip();

            // Use perMinutes instead of ->decayMinutes() for compatibility
            return Limit::perMinutes(1440, 200)
                ->by($key)
                ->response(function () {
                    return response()->json([
                        'ok'    => false,
                        'error' => 'Daily limit reached (200). Try again later.',
                    ], 429);
                });
        });
    }
}
