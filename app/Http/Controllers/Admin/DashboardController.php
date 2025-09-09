<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Carbon;

use App\Models\User;
use App\Models\UserLimit;

// These are optional; the code checks tables first.
use App\Models\AnalyzeLog;   // table: analyze_logs (history / traffic)
use App\Models\OpenAiUsage;  // table: open_ai_usages (tokens / cost)

class DashboardController extends Controller
{
    /** Render the Blade (namespaced SX Nova) */
    public function index()
    {
        // You can pass a quick limits summary for the sidebar
        $limitsSummary = $this->limitsSummary();
        return view('admin.dashboard', compact('limitsSummary'));
    }

    /** JSON used by the dashboard for live updates (10s) */
    public function live(Request $request)
    {
        // tiny caches to avoid hammering DB
        $kpis     = Cache::remember('dash:kpis', 8, fn () => $this->kpis());
        $services = Cache::remember('dash:services', 20, fn () => $this->services());
        $traffic  = Cache::remember('dash:traffic', 30, fn () => $this->traffic30());
        $history  = Cache::remember('dash:history', 12, fn () => $this->historyLatest());

        return response()->json(compact('kpis', 'services', 'traffic', 'history'));
    }

    /** Drawer: per-user live payload */
    public function userLive(User $user)
    {
        $limit = $user->limit()->first();
        return response()->json([
            'user'  => [
                'id'           => $user->id,
                'email'        => $user->email,
                'last_seen_at' => optional($user->last_seen_at)->toDateTimeString(),
                'last_ip'      => $user->last_ip,
                'last_country' => $user->last_country,
                'last_login_at'=> optional($user->last_login_at ?? null)->toDateTimeString(),
                'last_logout_at'=> optional($user->last_logout_at ?? null)->toDateTimeString(),
            ],
            'limit' => $limit ? [
                'daily_limit' => (int) $limit->daily_limit,
                'is_enabled'  => (bool) $limit->is_enabled,
                'reason'      => $limit->reason,
            ] : ['daily_limit' => 200, 'is_enabled' => true, 'reason' => ''],
        ]);
    }

    // ------------------------
    // Helpers
    // ------------------------

    private function kpis(): array
    {
        $now = Carbon::now();

        // totals
        $totalUsers = User::query()->count();

        // presence
        $active5m = User::query()
            ->whereNotNull('last_seen_at')
            ->where('last_seen_at', '>=', $now->clone()->subMinutes(5))
            ->count();

        // searches today, DAU, MAU from analyze_logs (if exists)
        $searchesToday = 0; $dau = 0; $mau = 0; $active24h = 0;
        if (Schema::hasTable('analyze_logs')) {
            $searchesToday = DB::table('analyze_logs')
                ->whereDate('created_at', $now->toDateString())->count();

            $dau = DB::table('analyze_logs')
                ->where('created_at', '>=', $now->clone()->subDay())
                ->distinct('user_id')->count('user_id');

            $mau = DB::table('analyze_logs')
                ->where('created_at', '>=', $now->clone()->subDays(30))
                ->distinct('user_id')->count('user_id');

            $active24h = DB::table('analyze_logs')
                ->where('created_at', '>=', $now->clone()->subDay())->count();
        }

        // OpenAI usage last 24h if table exists
        $cost24h = 0.0; $tokens24h = 0;
        if (Schema::hasTable('open_ai_usages')) {
            $q = DB::table('open_ai_usages')
                ->where('created_at', '>=', $now->clone()->subDay());

            // flexible column names
            $tokens24h = (int) ($q->sum('tokens') ?: $q->sum(DB::raw('prompt_tokens + completion_tokens')) ?: 0);
            $cost24h   = (float) ($q->sum('cost_usd') ?: $q->sum('cost') ?: $q->sum('usd_cost') ?: 0.0);
        }

        return [
            'searchesToday' => $searchesToday,
            'totalUsers'    => $totalUsers,
            'active5m'      => $active5m,
            'active24h'     => $active24h,
            'dau'           => $dau,
            'mau'           => $mau,
            'tokens24h'     => $tokens24h,
            'cost24h'       => round($cost24h, 4),
        ];
    }

    private function services(): array
    {
        $services = [];

        // DB ping latency
        try {
            $t0 = microtime(true);
            DB::select('SELECT 1');
            $lat = (int) round((microtime(true) - $t0) * 1000);
            $services[] = ['name' => 'Database', 'ok' => true, 'latency_ms' => $lat];
        } catch (\Throwable $e) {
            $services[] = ['name' => 'Database', 'ok' => false, 'latency_ms' => null];
        }

        // Queue backlog (if jobs table exists)
        if (Schema::hasTable('jobs')) {
            try {
                $count = (int) DB::table('jobs')->count();
                $services[] = ['name' => 'Queue', 'ok' => true, 'latency_ms' => $count.' pending'];
            } catch (\Throwable $e) {
                $services[] = ['name' => 'Queue', 'ok' => false, 'latency_ms' => null];
            }
        }

        // Scheduler heartbeat (set by kernel schedule)
        $beatAt = Cache::get('dash:heartbeat_at'); // set every minute
        if ($beatAt) {
            $diff = Carbon::parse($beatAt)->diffInSeconds(now());
            $services[] = ['name' => 'Scheduler', 'ok' => $diff <= 120, 'latency_ms' => $diff.'s'];
        } else {
            $services[] = ['name' => 'Scheduler', 'ok' => false, 'latency_ms' => null];
        }

        // OpenAI API heartbeat (recent usage within 6h)
        if (Schema::hasTable('open_ai_usages')) {
            $apiOk = DB::table('open_ai_usages')->where('created_at', '>=', now()->subHours(6))->exists();
            $services[] = ['name' => 'OpenAI API', 'ok' => (bool) $apiOk, 'latency_ms' => null];
        }

        return $services;
    }

    private function traffic30(): array
    {
        if (!Schema::hasTable('analyze_logs')) {
            return [];
        }

        $start = now()->startOfDay()->subDays(29);
        $raw = DB::table('analyze_logs')
            ->select(DB::raw('DATE(created_at) as day'), DB::raw('COUNT(*) as c'))
            ->where('created_at', '>=', $start)
            ->groupBy(DB::raw('DATE(created_at)'))
            ->orderBy('day', 'asc')
            ->pluck('c', 'day'); // [ 'YYYY-MM-DD' => count ]

        // Fill missing days
        $out = [];
        for ($d = $start->copy(); $d <= now()->startOfDay(); $d->addDay()) {
            $key = $d->toDateString();
            $out[] = ['day' => $key, 'count' => (int) ($raw[$key] ?? 0)];
        }
        return $out;
    }

    private function historyLatest(): array
    {
        if (!Schema::hasTable('analyze_logs')) {
            return [];
        }

        // Try to pull columns flexibly: url/query/tool/tokens/cost
        $rows = DB::table('analyze_logs as a')
            ->leftJoin('users as u', 'u.id', '=', 'a.user_id')
            ->orderByDesc('a.created_at')
            ->limit(100)
            ->get([
                'a.created_at',
                'u.email',
                DB::raw("COALESCE(a.url, a.query, a.input, a.page_url) as display"),
                DB::raw("COALESCE(a.tool, a.tool_name, a.section) as tool"),
                DB::raw("COALESCE(a.tokens, a.total_tokens, 0) as tokens"),
                DB::raw("COALESCE(a.cost, a.cost_usd, a.usd_cost, 0) as cost"),
            ]);

        return $rows->map(function ($r) {
            return [
                'when'    => Carbon::parse($r->created_at)->format('Y-m-d H:i'),
                'user'    => $r->email ?? '—',
                'display' => (string) ($r->display ?? '—'),
                'tool'    => (string) ($r->tool ?? '—'),
                'tokens'  => (int) ($r->tokens ?? 0),
                'cost'    => number_format((float) ($r->cost ?? 0), 4, '.', ''),
            ];
        })->toArray();
    }

    private function limitsSummary(): array
    {
        if (!Schema::hasTable('user_limits')) {
            return ['enabled' => 0, 'disabled' => 0, 'default' => 200];
        }
        $enabled  = (int) DB::table('user_limits')->where('is_enabled', true)->count();
        $disabled = (int) DB::table('user_limits')->where('is_enabled', false)->count();
        $default  = 200;
        return compact('enabled', 'disabled', 'default');
    }

    // ---------------------------------------------------------------------
    // ADD-ONLY: Users — Live table & Sessions JSON
    // ---------------------------------------------------------------------

    /**
     * Live users table (searchable). Returns: rows[id,name,email,banned,last_seen,ip,country,limit,enabled]
     */
    public function usersTable(Request $request)
    {
        $limit = max(5, min(50, (int) $request->input('limit', 20)));
        $q     = trim((string) $request->input('q', ''));

        $query = DB::table('users as u')
            ->leftJoin('user_limits as l', 'l.user_id', '=', 'u.id')
            ->select([
                'u.id','u.name','u.email','u.is_banned','u.last_seen_at','u.last_ip','u.last_country',
                DB::raw('COALESCE(l.daily_limit, 200) as daily_limit'),
                DB::raw('COALESCE(l.is_enabled, 1) as is_enabled'),
            ])
            ->when($q !== '', function ($w) use ($q) {
                $w->where(function ($x) use ($q) {
                    $x->where('u.email','like',"%{$q}%")
                      ->orWhere('u.name','like',"%{$q}%")
                      ->orWhere('u.last_ip','like',"%{$q}%");
                });
            })
            ->orderByRaw('u.last_seen_at IS NULL, u.last_seen_at DESC')
            ->limit($limit)
            ->get();

        $rows = $query->map(function ($r) {
            return [
                'id'        => (int) $r->id,
                'name'      => $r->name ?? '—',
                'email'     => $r->email ?? '—',
                'banned'    => (bool) $r->is_banned,
                'last_seen' => $r->last_seen_at ? Carbon::parse($r->last_seen_at)->diffForHumans() : '—',
                'ip'        => $r->last_ip ?? '—',
                'country'   => $r->last_country ?? '—',
                'limit'     => (int) $r->daily_limit,
                'enabled'   => (bool) $r->is_enabled,
            ];
        });

        return response()->json(['rows' => $rows], 200);
    }

    /**
     * Recent login sessions for a user (if user_sessions table exists).
     * Returns: rows[login_at,logout_at,ip,country,ua]
     */
    public function userSessions(User $user)
    {
        if (!Schema::hasTable('user_sessions')) {
            return response()->json(['rows' => []], 200);
        }

        $S = DB::table('user_sessions')
            ->where('user_id', $user->id)
            ->orderByDesc('login_at')
            ->limit(20)
            ->get(['login_at','logout_at','ip','country','user_agent']);

        $rows = $S->map(fn($s) => [
            'login_at'  => $s->login_at ? Carbon::parse($s->login_at)->toDateTimeString() : '—',
            'logout_at' => $s->logout_at ? Carbon::parse($s->logout_at)->toDateTimeString() : '—',
            'ip'        => $s->ip ?? '—',
            'country'   => $s->country ?? '—',
            'ua'        => $s->user_agent ? mb_strimwidth($s->user_agent, 0, 80, '…') : '—',
        ]);

        return response()->json(['rows' => $rows], 200);
    }
}
