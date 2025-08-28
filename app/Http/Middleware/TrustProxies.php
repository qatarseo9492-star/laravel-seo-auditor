<?php

namespace App\Http\Middleware;

use Illuminate\Http\Middleware\TrustProxies as Middleware;
// Use Symfony's Request constants for forwarded headers
use Symfony\Component\HttpFoundation\Request as SymfonyRequest;

class TrustProxies extends Middleware
{
    /**
     * Trust all upstream proxies (Cloudflare/Nginx/Load balancers).
     * If you prefer, list specific IPs/CIDRs instead of '*'.
     */
    protected $proxies = '*';

    /**
     * Tell Laravel which X-Forwarded-* headers to honor.
     * Equivalent to HEADER_X_FORWARDED_ALL (bitmask 1|2|4|8 = 15).
     */
    protected $headers =
          SymfonyRequest::HEADER_X_FORWARDED_FOR
        | SymfonyRequest::HEADER_X_FORWARDED_HOST
        | SymfonyRequest::HEADER_X_FORWARDED_PROTO
        | SymfonyRequest::HEADER_X_FORWARDED_PORT;
}
