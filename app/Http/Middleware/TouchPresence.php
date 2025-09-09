// inside handle($request, Closure $next)
$response = $next($request);

try {
    if (auth()->check()) {
        $u = auth()->user();
        $ip = $request->headers->get('CF-Connecting-IP') ?: $request->ip();
        $cc = $request->headers->get('CF-IPCountry')    // Cloudflare two-letter code
           ?: $request->server('HTTP_CF_IPCOUNTRY')
           ?: null;

        $u->last_seen_at = now();
        $u->last_ip      = $ip;
        if ($cc && $cc !== 'XX') {
            $u->last_country = $cc;
        }
        $u->save();
    }
} catch (\Throwable $e) {
    // swallow; presence must never break request
}

return $response;
