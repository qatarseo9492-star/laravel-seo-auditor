<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TouchPresence
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        try {
            if ($request->user()) {
                $user = $request->user();
                $user->last_seen_at = now();
                if ($ip = $request->ip()) { $user->last_ip = $ip; }
                // If you capture country via GeoIP, set $user->last_country = ...
                $user->saveQuietly();
            }
        } catch (\Throwable $e) {}

        return $response;
    }
}
