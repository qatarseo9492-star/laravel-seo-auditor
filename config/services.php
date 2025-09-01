<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    */

    'mailgun' => [
        'domain'   => env('MAILGUN_DOMAIN'),
        'secret'   => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
        'scheme'   => 'https',
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key'    => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Google PageSpeed Insights
    |--------------------------------------------------------------------------
    */
    'pagespeed' => [
        'key'       => env('PAGESPEED_API_KEY', env('GOOGLE_PSI_KEY')),
        'endpoint'  => env('PAGESPEED_ENDPOINT', 'https://www.googleapis.com/pagespeedonline/v5/runPagespeed'),
        'timeout'   => (int) env('PAGESPEED_TIMEOUT', 25),
        'cache_ttl' => (int) env('PAGESPEED_CACHE_TTL', 120),
    ],

    // Alias so older code using config('services.psi.*') still works.
    'psi' => [
        'key'       => env('GOOGLE_PSI_KEY', env('PAGESPEED_API_KEY')),
        'endpoint'  => env('PAGESPEED_ENDPOINT', 'https://www.googleapis.com/pagespeedonline/v5/runPagespeed'),
        'timeout'   => (int) env('PAGESPEED_TIMEOUT', 25),
        'cache_ttl' => (int) env('PAGESPEED_CACHE_TTL', 120),
    ],

    /*
    |--------------------------------------------------------------------------
    | OpenAI (for Content Optimization)
    |--------------------------------------------------------------------------
    | The controller reads these via config() with env() fallback.
    | You can swap model/base_url/org in .env without touching code.
    */
    'openai' => [
        'key'      => env('OPENAI_API_KEY'),
        'base_url' => env('OPENAI_BASE_URL', 'https://api.openai.com/v1'),
        'model'    => env('OPENAI_MODEL', 'gpt-4o-mini'),
        'timeout'  => (int) env('OPENAI_TIMEOUT', 60),
        'org'      => env('OPENAI_ORG'), // <-- added (optional, for enterprise/org accounts)
    ],

];
