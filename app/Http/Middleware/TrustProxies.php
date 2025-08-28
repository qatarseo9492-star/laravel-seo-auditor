<?php

namespace App\Http\Middleware;

use Illuminate\Http\Middleware\TrustProxies as Middleware;
use Illuminate\Http\Request;

class TrustProxies extends Middleware
{
    // Trust all proxies (safe when you control Cloudflare/hosting)
    protected $proxies = '*';

    // Use all X-Forwarded-* headers (this respects CF-Connecting-IP too)
    protected $headers = Request::HEADER_X_FORWARDED_ALL;
}
