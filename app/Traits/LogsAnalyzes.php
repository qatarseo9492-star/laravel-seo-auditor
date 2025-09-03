<?php

namespace App\Traits;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

trait LogsAnalyzes
{
    /**
     * Log an analysis run to analyze_logs using the current column names.
     *
     * @param  string      $tool     Canonical tool name (e.g., 'semantic', 'technical_seo', 'keyword_intelligence', 'content_optimization', 'content_engine', 'psi')
     * @param  string      $url      The analyzed URL
     * @param  bool        $success  Whether the run succeeded
     * @param  int|null    $tokens   Optional token usage
     */
    protected function logAnalyze(string $tool, string $url, bool $success = true, ?int $tokens = null): void
    {
        if (!Auth::check()) {
            return;
        }

        $user = Auth::user();

        // Try Cloudflare / proxy headers first, then fall back to Laravel's IP
        $ip = request()->headers->get('CF-Connecting-IP')
            ?? request()->headers->get('X-Forwarded-For')
            ?? request()->ip();

        $country = request()->headers->get('CF-IPCountry')
            ?? request()->headers->get('X-Country')
            ?? null;

        DB::table('analyze_logs')->insert([
            'user_id'     => $user->id,
            'tool'        => $tool,        // ✅ column exists
            'url'         => $url,
            'ip_address'  => $ip,          // ✅ column exists (used to be 'ip')
            'country'     => $country,
            'successful'  => $success,     // ✅ column exists (used to be 'success')
            'tokens_used' => $tokens,
            'created_at'  => now(),
            'updated_at'  => now(),
        ]);
    }
}
