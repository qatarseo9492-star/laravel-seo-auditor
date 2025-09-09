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

                // Prefer Cloudflare headers when present
                $ip = $request->headers->get('CF-Connecting-IP') ?: $request->ip();
                $cc = $request->headers->get('CF-IPCountry')
                    ?: $request->server('HTTP_CF_IPCOUNTRY')
                    ?: null;

                // Touch presence on the user record
                $user->last_seen_at = now();
                $user->last_ip      = $ip;
                if ($cc && $cc !== 'XX') {
                    $user->last_country = $cc; // 2-letter code; store as-is
                }
                $user->save();

                // Optionally track sessions if table exists
                if (Schema::hasTable('user_sessions')) {
                    $sid = $request->session()->getId();
                    $now = now();

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
                                'updated_at' => $now,
                            ]);
                    }
                }
            }
        } catch (\Throwable $e) {
            // Never break the request due to presence logging
        }

        return $response;
    }
}
