<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class TouchPresence
{
    /**
     * Update the authenticated user's presence on every request and
     * (optionally) upsert a user_sessions row (if that table exists).
     */
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        try {
            if (auth()->check()) {
                $user = auth()->user();

                // ----------------- Resolve IP & Country (prefer edge/CDN headers) -----------------
                $ip = $request->headers->get('CF-Connecting-IP')
                    ?: self::firstPublicForwardedFor($request)
                    ?: $request->ip();

                $cc = $request->headers->get('CF-IPCountry')
                    ?: $request->server('HTTP_CF_IPCOUNTRY')
                    ?: null;

                // ----------------- Touch presence on users table (if columns exist) -----------------
                $hasLastSeen   = Schema::hasColumn('users', 'last_seen_at');
                $hasLastIp     = Schema::hasColumn('users', 'last_ip');
                $hasLastCountry= Schema::hasColumn('users', 'last_country');

                $shouldSave = false;
                $now = now();

                if ($hasLastSeen) {
                    // Throttle to avoid a write on every request: update if >60s old or null
                    $stale = ! $user->last_seen_at || $user->last_seen_at->lt($now->copy()->subSeconds(60));
                    if ($stale) {
                        $user->last_seen_at = $now;
                        $shouldSave = true;
                    }
                }

                if ($hasLastIp && $ip && $ip !== $user->last_ip) {
                    $user->last_ip = $ip;
                    $shouldSave = true;
                }

                if ($hasLastCountry && $cc && $cc !== 'XX' && $cc !== ($user->last_country ?? null)) {
                    $user->last_country = $cc; // 2-letter country code
                    $shouldSave = true;
                }

                if ($shouldSave) {
                    // Avoid triggering observers/events
                    $user->saveQuietly();
                }

                // ----------------- Optional: track sessions if table exists -----------------
                if (Schema::hasTable('user_sessions')) {
                    $sid = optional($request->session())->getId();
                    if ($sid) {
                        $ua  = substr((string) $request->userAgent(), 0, 500);
                        $now = now();

                        // Insert if missing; otherwise update IP/country/UA/updated_at
                        $exists = DB::table('user_sessions')
                            ->where('session_id', $sid)
                            ->where('user_id', $user->id)
                            ->exists();

                        if (! $exists) {
                            DB::table('user_sessions')->insert([
                                'user_id'    => $user->id,
                                'session_id' => $sid,
                                'login_at'   => $now,
                                'logout_at'  => null,
                                'ip'         => $ip,
                                'country'    => $cc,
                                'user_agent' => $ua,
                                'created_at' => $now,
                                'updated_at' => $now,
                            ]);
                        } else {
                            DB::table('user_sessions')
                                ->where('session_id', $sid)
                                ->where('user_id', $user->id)
                                ->update([
                                    'ip'         => $ip,
                                    'country'    => $cc,
                                    'user_agent' => $ua,
                                    'updated_at' => $now,
                                ]);
                        }
                    }
                }
            }
        } catch (\Throwable $e) {
            // Never break the request due to presence logging
        }

        return $response;
    }

    /**
     * Pull the first public IP from X-Forwarded-For (if present).
     */
    private static function firstPublicForwardedFor(Request $request): ?string
    {
        $xff = $request->headers->get('X-Forwarded-For');
        if (!$xff) return null;

        // XFF may be a comma-separated list; take the first non-private IP.
        foreach (array_map('trim', explode(',', $xff)) as $candidate) {
            if ($candidate === '') continue;
            // Skip RFC1918/4193 private ranges
            if (filter_var($candidate, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
                return $candidate;
            }
        }
        // Fall back to first entry if none matched public filter
        $first = trim(explode(',', $xff)[0] ?? '');
        return $first !== '' ? $first : null;
    }
}
