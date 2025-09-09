<?php

namespace App\Support;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AnalysisLogger
{
    /**
     * Log an analysis run into analyze_logs, only writing columns that exist.
     */
    public static function log(string $tool, ?string $url = null, ?int $tokens = null, ?float $costUsd = null, array $extra = []): void
    {
        try {
            $req = request();

            $data = [
                'user_id'    => auth()->id(),
                'tool'       => $tool,
                'url'        => $url,
                'successful' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ];

            // IP & country if you track them
            if (Schema::hasColumn('analyze_logs', 'ip_address')) {
                $data['ip_address'] = $req?->headers->get('CF-Connecting-IP') ?: $req?->ip();
            }
            if (Schema::hasColumn('analyze_logs', 'country')) {
                $data['country'] = $req?->headers->get('CF-IPCountry') ?: null;
            }

            // Optional tokens/cost if your table has them
            if (Schema::hasColumn('analyze_logs', 'tokens_used') && $tokens !== null) {
                $data['tokens_used'] = $tokens;
            }
            if (Schema::hasColumn('analyze_logs', 'tokens') && $tokens !== null) {
                $data['tokens'] = $tokens;
            }
            if (Schema::hasColumn('analyze_logs', 'cost_usd') && $costUsd !== null) {
                $data['cost_usd'] = $costUsd;
            }
            if (Schema::hasColumn('analyze_logs', 'cost') && $costUsd !== null) {
                $data['cost'] = $costUsd;
            }

            DB::table('analyze_logs')->insert($data + $extra);
        } catch (\Throwable $e) {
            // Never break the user flow because of logging
        }
    }
}
