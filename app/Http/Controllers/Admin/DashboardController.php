<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        $kpis = $this->computeKpis();
        $mrr = null;

        if (Schema::hasTable('subscriptions')) {
            $sum = DB::table('subscriptions')
                ->where('status', 'active')
                ->sum('mrr_cents');
            $mrr = '$' . number_format((float) $sum / 100, 2);
        }

        return view('admin.dashboard', [
            'kpis' => $kpis,
            'mrr'  => $mrr,
        ]);
    }

    public function live(Request $request)
    {
        try { $kpis     = $this->computeKpis();   } catch (\Throwable $e) { $kpis     = []; }
        try { $services = $this->serviceHealth(); } catch (\Throwable $e) { $services = []; }
        try { $history  = $this->recentHistory(); } catch (\Throwable $e) { $history  = []; }
        try { $traffic  = $this->trafficSeries(); } catch (\Throwable $e) { $traffic  = []; }

        return response()->json(compact('kpis','services','history','traffic'));
    }

    /* ========================= KPIs ========================= */

    private function computeKpis(): array
    {
        $today = now()->startOfDay();
        $k = [
            'searchesToday' => 0,
            'totalUsers'    => 0,
            'cost24h'       => 0.0,
            'tokens24h'     => 0,
            'dau'           => 0,
            'mau'           => 0,
            'active5m'      => 0,
            'dailyLimit'    => 100,
        ];

        if (Schema::hasTable('users')) {
            $k['totalUsers'] = (int) User::count();
            if (Schema::hasColumn('users', 'last_seen_at')) {
                $k['active5m'] = (int) User::where('last_seen_at', '>=', now()->subMinutes(5))->count();
            }
        }

        if (Schema::hasTable('analyze_logs')) {
            $k['searchesToday'] = (int) DB::table('analyze_logs')->where('created_at', '>=', $today)->count();
            $k['dau'] = (int) DB::table('analyze_logs')->where('created_at', '>=', now()->subDay())->distinct('user_id')->count('user_id');
            $k['mau'] = (int) DB::table('analyze_logs')->where('created_at', '>=', now()->subDays(30))->distinct('user_id')->count('user_id');
        } elseif (Schema::hasTable('analysis_cache')) {
            $k['searchesToday'] = (int) DB::table('analysis_cache')->where('created_at', '>=', $today)->count();
            $k['dau'] = (int) DB::table('analysis_cache')->where('created_at', '>=', now()->subDay())->distinct('user_id')->count('user_id');
            $k['mau'] = (int) DB::table('analysis_cache')->where('created_at', '>=', now()->subDays(30))->distinct('user_id')->count('user_id');
        }

        $usageTable = Schema::hasTable('open_ai_usages')
            ? 'open_ai_usages'
            : (Schema::hasTable('openai_usage') ? 'openai_usage' : null);

        if ($usageTable) {
            if (Schema::hasColumn($usageTable, 'cost_usd')) {
                $k['cost24h'] = (float) DB::table($usageTable)->where('created_at', '>=', now()->subDay())->sum('cost_usd');
            } elseif (Schema::hasColumn($usageTable, 'cost')) {
                $k['cost24h'] = (float) DB::table($usageTable)->where('created_at', '>=', now()->subDay())->sum('cost');
            }
            if (Schema::hasColumn($usageTable, 'tokens')) {
                $k['tokens24h'] = (int) DB::table($usageTable)->where('created_at', '>=', now()->subDay())->sum('tokens');
            }
        }

        if (Schema::hasTable('user_limits') && Schema::hasColumn('user_limits', 'daily_limit')) {
            $avg = DB::table('user_limits')->avg('daily_limit');
            $k['dailyLimit'] = (int) ($avg ?: 100);
        }

        return $k;
    }

    private function serviceHealth(): array
    {
        $out = [];

        $dbStart = microtime(true);
        try {
            DB::select('SELECT 1');
            $out[] = ['name' => 'Database','ok' => true,'latency_ms' => (int)((microtime(true)-$dbStart)*1000)];
        } catch (\Throwable $e) {
            $out[] = ['name' => 'Database','ok' => false,'latency_ms' => null];
        }

        $out[] = ['name' => 'Queue',  'ok' => Schema::hasTable('jobs') && Schema::hasTable('failed_jobs'), 'latency_ms' => null];
        $out[] = ['name' => 'OpenAI', 'ok' => Schema::hasTable('open_ai_usages') || Schema::hasTable('openai_usage'), 'latency_ms' => null];

        return $out;
    }

    /* ======================= History (PATCHED) ======================== */
    /**
     * Minimal, guaranteed feed: show the latest 50 analysis runs directly
     * from analyze_logs so they can't be drowned out by sessions/signups.
     */
    private function recentHistory(): array
    {
        if (!Schema::hasTable('analyze_logs')) {
            return [];
        }

        $q = DB::table('analyze_logs as a');
        $select = ['a.id','a.created_at','a.tool','a.url'];

        // Optional tokens/cost columns (only if they exist)
        if (Schema::hasColumn('analyze_logs','tokens_used')) $select[] = 'a.tokens_used';
        if (Schema::hasColumn('analyze_logs','tokens'))      $select[] = 'a.tokens';
        if (Schema::hasColumn('analyze_logs','cost_usd'))    $select[] = 'a.cost_usd';
        if (Schema::hasColumn('analyze_logs','cost'))        $select[] = 'a.cost';

        // Join users if user_id exists
        if (Schema::hasColumn('analyze_logs', 'user_id') && Schema::hasTable('users')) {
            $q->leftJoin('users as u', 'u.id', '=', 'a.user_id');
            $select[] = 'u.email';
            $select[] = 'u.name';
        }

        // Pull the newest 50 analyses
        $rows = $q->orderByDesc('a.id')->limit(50)->get($select);

        // Map rows to the UI structure
        $out = [];
        foreach ($rows as $r) {
            $user   = $r->name ?? $r->email ?? '—';
            $tokens = $r->tokens_used ?? $r->tokens ?? null;
            $cost   = $r->cost_usd ?? $r->cost ?? 0;

            $out[] = [
                'ts'      => $r->created_at,
                'when'    => $this->fmt($r->created_at),
                'user'    => $user,
                'display' => $r->url ? ('Analyzed URL '.$this->shortUrl($r->url)) : 'Analysis',
                'tool'    => $r->tool ?: 'semantic',
                'tokens'  => $tokens ?? '—',
                'cost'    => number_format((float) $cost, 4),
            ];
        }

        return $out;
    }

    /* ====================== Traffic ========================= */

    private function trafficSeries(): array
    {
        try { if (Schema::hasTable('analyze_logs'))   return $this->seriesFromTable('analyze_logs'); } catch (\Throwable $e) {}
        try { if (Schema::hasTable('analysis_cache')) return $this->seriesFromTable('analysis_cache'); } catch (\Throwable $e) {}

        $usageTable = Schema::hasTable('open_ai_usages')
            ? 'open_ai_usages'
            : (Schema::hasTable('openai_usage') ? 'openai_usage' : null);
        if ($usageTable) { try { return $this->seriesFromTable($usageTable); } catch (\Throwable $e) {} }

        return [];
    }

    private function seriesFromTable(string $table): array
    {
        $days = 14;
        $start = now()->subDays($days - 1)->startOfDay();

        $raw = DB::table($table)
            ->select(DB::raw('DATE(created_at) as d'), DB::raw('COUNT(*) as c'))
            ->where('created_at', '>=', $start)
            ->groupBy('d')
            ->orderBy('d')
            ->get();

        $map = $raw->keyBy('d');
        $out = [];
        for ($i = 0; $i < $days; $i++) {
            $d = $start->copy()->addDays($i)->toDateString();
            $out[] = ['day' => $d, 'count' => (int) ($map[$d]->c ?? 0)];
        }
        return $out;
    }

    /* ====================== Helpers ========================= */

    private function shortUrl(?string $url): string
    {
        if (!$url) return '';
        try {
            $p = parse_url($url);
            $host = $p['host'] ?? '';
            $path = isset($p['path']) ? rtrim($p['path'], '/') : '';
            if (strlen($path) > 32) {
                $path = function_exists('mb_strimwidth') ? mb_strimwidth($path, 0, 32, '…') : substr($path, 0, 32).'…';
            }
            return $host . $path;
        } catch (\Throwable $e) {
            return $url;
        }
    }

    private function fmt($ts): ?string
    {
        if (!$ts) return null;
        try {
            if ($ts instanceof \DateTimeInterface) return Carbon::instance($ts)->format('Y-m-d H:i');
            if (is_numeric($ts) && strlen((string) $ts) <= 10) return Carbon::createFromTimestamp((int)$ts)->format('Y-m-d H:i');
            return Carbon::parse((string) $ts)->format('Y-m-d H:i');
        } catch (\Throwable $e) {
            return is_string($ts) ? $ts : null;
        }
    }
}
