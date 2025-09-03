<?php

namespace App\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{
    // ... other properties

    protected $middlewareAliases = [
        'admin' => \App\Http\Middleware\AdminCheck::class,
        'ban' => \App\Http\Middleware\BanCheck::class,
        'quota' => \App\Http\Middleware\QuotaGuard::class,
        'presence' => \App\Http\Middleware\TouchPresence::class,
        // ... other aliases
    ];

    protected $middlewareGroups = [
        'web' => [
            // ... other middleware
            \App\Http\Middleware\BanCheck::class,
            \App\Http\Middleware\TouchPresence::class,
        ],
        'api' => [
            // ... other middleware
            'quota', // Enforce quotas on API routes
        ],
    ];

    // ... rest of the file
}
