<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class ContentDetectionServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../../config/content-detection.php', 'content-detection');
    }

    public function boot(): void
    {
        // Publish config
        $this->publishes([
            __DIR__ . '/../../config/content-detection.php' => config_path('content-detection.php'),
        ], 'content-detection-config');

        // Named rate limiter: 100 requests per hour per user (IP if guest)
        RateLimiter::for('content-detection', function (Request $request) {
            $key = optional($request->user())->id ?: $request->ip();
            return Limit::perHour(config('content-detection.rate_limit.per_hour', 100))->by($key);
        });
    }
}
