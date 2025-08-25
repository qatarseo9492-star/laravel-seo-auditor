'pagespeed' => [
    'key'       => env('PAGESPEED_API_KEY'),
    'endpoint'  => env('PAGESPEED_ENDPOINT', 'https://www.googleapis.com/pagespeedonline/v5/runPagespeed'),
    'timeout'   => env('PAGESPEED_TIMEOUT', 20),
    'cache_ttl' => env('PAGESPEED_CACHE_TTL', 60),
],
