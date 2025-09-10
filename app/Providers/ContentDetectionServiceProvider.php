<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class ContentDetectionServiceProvider extends ServiceProvider
{
    /**
     * Register application services and merge package/app config.
     */
    public function register(): void
    {
        // Merge default config so users can override keys in config/content-detection.php
        $cfg = realpath(__DIR__ . '/../../config/content-detection.php');
        if ($cfg && file_exists($cfg)) {
            $this->mergeConfigFrom($cfg, 'content-detection');
        }
    }

    /**
     * Bootstrap any application services (publish & rate limiters).
     */
    public function boot(): void
    {
        // Offer a publishable config for "php artisan vendor:publish --tag=content-detection-config"
        if ($this->app->runningInConsole()) {
            $src = realpath(__DIR__ . '/../../config/content-detection.php');
            if ($src && file_exists($src)) {
                $this->publishes([
                    $src => config_path('content-detection.php'),
                ], 'content-detection-config');
            }
        }

        // Named rate limiter: "content-detection"
        // Usage on routes: ->middleware('throttle:content-detection')
        RateLimiter::for('content-detection', function (Request $request) {
            // Prefer Cloudflare IP if present, then normal IP
            $ip = $request->headers->get('CF-Connecting-IP') ?: $request->ip();
            // Key per authenticated user; guest falls back to IP
            $key = optional($request->user())->id ?? $ip;

            $perHour = (int) config('content-detection.rate_limit.per_hour', 100);

            return Limit::perHour($perHour)->by($key);
        });
    }
}
