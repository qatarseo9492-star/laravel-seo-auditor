<?php

namespace App\Http\Middleware;

use Illuminate\Http\Middleware\TrustProxies as Middleware;
use Illuminate\Http\Request;

/**
 * Trust upstream proxies/load balancers (Cloudways/Cloudflare)
 * so Laravel sees the real client IP and protocol.
 */
class TrustProxies extends Middleware
{
    /**
     * Trust all proxies. (Safe when you control your edge like Cloudflare/Nginx.)
     * If you prefer to pin specific IPs, replace '*' with an array of IPs/CIDRs.
     *
     * @var array<int, string>|string|null
     */
    protected $proxies = '*';

    /**
     * Use all standard X-Forwarded-* headers.
     * This ensures $request->ip(), $request->secure(), etc. are correct.
     *
     * @var int
     */
    protected $headers = Request::HEADER_X_FORWARDED_ALL;
}
