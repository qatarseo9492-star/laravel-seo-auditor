<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\RateLimiter;   // optional
use Illuminate\Cache\RateLimiting\Limit;      // optional

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Bind interfaces, singletons, etc. (optional)
    }

    public function boot(): void
    {
        // Optional: rate limit used by the detector API routes
        if (class_exists(RateLimiter::class)) {
            RateLimiter::for('content-detection', function ($request) {
                return [Limit::perHour(100)->by(optional($request->user())->id ?: $request->ip())];
            });
        }
    }
}
