<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such as
    | Mailgun, Postmark, AWS SES, Resend, Slack, OAuth providers, etc.
    | Values should live in your .env and be referenced with env().
    |
    */

    // === Google (PageSpeed Insights proxy) ===
    'google' => [
        // Keep the key only in .env as GOOGLE_API_KEY
        'page_speed_key' => env('GOOGLE_API_KEY'),
    ],

    // === Mail services (optional, leave as-is if unused) ===
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

    'resend' => [
        'key' => env('RESEND_KEY'),
    ],

    // === Slack notifications (optional) ===
    'slack' => [
        'notifications' => [
            'webhook_url' => env('SLACK_WEBHOOK_URL'),
        ],
    ],

    // === Example OAuth providers (optional) ===
    'github' => [
        'client_id'     => env('GITHUB_CLIENT_ID'),
        'client_secret' => env('GITHUB_CLIENT_SECRET'),
        'redirect'      => env('GITHUB_REDIRECT_URI'),
    ],

    'google_oauth' => [
        'client_id'     => env('GOOGLE_CLIENT_ID'),
        'client_secret' => env('GOOGLE_CLIENT_SECRET'),
        'redirect'      => env('GOOGLE_REDIRECT_URI'),
    ],

];
