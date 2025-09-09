<?php

namespace App\Support;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Schema;

class AnalysisLogger
{
    /**
     * Write a single analysis log row (safe on mixed schemas).
     *
     * @param string      $tool    e.g. 'psi', 'content_engine', 'keyword_intelligence', 'technical_seo', 'topic_cluster'
     * @param string|null $url
     * @param int|null    $tokens
     * @param float|null  $cost
     * @param array       $extra   e.g. ['user_id'=>..., 'successful'=>1]
     */
    public static function log(string $tool, ?string $url = null, ?int $tokens = null, ?float $cost = null, array $extra = []): void
    {
        try {
            if (!Schema::hasTable('analyze_logs')) {
                return; // nothing to do if the table doesn't exist
            }

            // Resolve columns that may or may not exist on this install
            $has = fn(string $col) => Schema::hasColumn('analyze_logs', $col);

            $tokensCol = $has('tokens_used') ? 'tokens_used' : ($has('tokens') ? 'tokens' : null);
            $costCol   = $has('cost_usd')    ? 'cost_usd'    : ($has('cost')   ? 'cost'   : null);

            $now   = now();
            $user  = $extra['user_id'] ?? Auth::id();
            $ip    = Request::header('CF-Connecting-IP') ?: Request::ip();
            $cc    = Request::header('CF-IPCountry') ?: Request::server('HTTP_CF_IPCOUNTRY');

            $row = [
                'user_id'    => $user,
                'tool'       => $tool,
                'url'        => $url,
                'ip_address' => $has('ip_address') ? $ip : null,
                'country'    => $has('country')    ? ($cc ?: null) : null,
                'successful' => $has('successful') ? (int) ($extra['successful'] ?? 1) : null,
                'created_at' => $now,
                'updated_at' => $now,
            ];

            if ($tokensCol !== null) $row[$tokensCol] = $tokens;
            if ($costCol   !== null) $row[$costCol]   = $cost;

            DB::table('analyze_logs')->insert($row);
        } catch (\Throwable $e) {
            // Never break the request due to logging
        }
    }
}
