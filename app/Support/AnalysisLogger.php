<?php

namespace App\Support;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AnalysisLogger
{
    public static function log(string $tool, ?string $url = null, ?int $tokens = null, ?float $costUsd = null, array $extra = []): void
    {
        try {
            $req = request();

            $notNullable = function (string $col): bool {
                if (!Schema::hasColumn('analyze_logs', $col)) return false;
                $c = DB::selectOne('SHOW COLUMNS FROM analyze_logs LIKE ?', [$col]);
                return $c && isset($c->Null) && $c->Null === 'NO';
            };

            $userId = auth()->id();
            if (Schema::hasColumn('analyze_logs','user_id') && $notNullable('user_id') && $userId === null) {
                $userId = DB::table('users')->min('id');
            }

            $data = [
                'user_id'    => $userId,
                'tool'       => $tool ?: 'semantic',
                'url'        => $url,
                'successful' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ];

            if (Schema::hasColumn('analyze_logs','ip_address')) {
                $ip = $req?->headers->get('CF-Connecting-IP') ?: $req?->ip();
                if ($ip === null && $notNullable('ip_address')) $ip = '127.0.0.1';
                $data['ip_address'] = $ip;
            }
            if (Schema::hasColumn('analyze_logs','country')) {
                $cc = $req?->headers->get('CF-IPCountry') ?: null;
                if (($cc === null || $cc === 'XX') && $notNullable('country')) $cc = 'XX';
                $data['country'] = $cc;
            }

            if (Schema::hasColumn('analyze_logs','tokens_used')) $data['tokens_used'] = $tokens ?? ($notNullable('tokens_used') ? 0 : null);
            if (Schema::hasColumn('analyze_logs','tokens'))      $data['tokens']      = $tokens ?? ($notNullable('tokens')      ? 0 : null);
            if (Schema::hasColumn('analyze_logs','cost_usd'))    $data['cost_usd']    = $costUsd ?? ($notNullable('cost_usd')   ? 0 : null);
            if (Schema::hasColumn('analyze_logs','cost'))        $data['cost']        = $costUsd ?? ($notNullable('cost')       ? 0 : null);

            DB::table('analyze_logs')->insert(array_merge($data, $extra));
        } catch (\Throwable $e) {
            // never block the request due to logging
        }
    }
}
