<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

use App\Models\User;
use App\Models\UserLimit;

class DashboardController extends Controller
{
    public function index()
    {
        $limitsSummary = $this->limitsSummary();
        return view('admin.dashboard', compact('limitsSummary'));
    }

    /** Live JSON payload consumed by dashboard (poll ~10s) */
    public function live(Request $request)
    {
        $kpis     = Cache::remember('dash:kpis', 8, fn () => $this->kpis());
        $services = Cache::remember('dash:services', 20, fn () => $this->services());
        $traffic  = Cache::remember('dash:traffic', 30, fn () => $this->traffic30());
        $history  = Cache::remember('dash:history', 12, fn () => $this->historyLatest());

        // NEW: top queries & error digest (additive, optional tables)
        $top     = Cache::remember('dash:top7d', 60, fn () => $this->topQueries7d());
        $errors  = Cache::remember('dash:errors24h', 60, fn () => $this->errorDigest24h());

        return response()->json(compact('kpis', 'services', 'traffic', 'history', 'top', 'errors'));
    }

    /** Drawer: per-user live payload */
    public function userLive(User $user)
    {
        $limit = $user->limit()->first();
        return response()->json([
            'user'  => [
                'id'             => $user->id,
                'email'          => $user->email,
                'name'           => $user->name,
                'last_seen_at'   => optional($user->last_seen_at)->toDateTimeString(),
                'last_ip'        => $user->last_ip,
                'last_country'   => $user->last_country,
                'last_login_at'  => optional($user->last_login_at ?? null)->toDateTimeString(),
                'last_logout_at' => optional($user->last_logout_at ?? null)->toDateTimeString(),
            ],
            'limit' => $limit ? [
                'daily_limit' => (int) $limit->daily_limit,
                'is_enabled'  => (bool) $limit->is_enabled,
                'reason'      => $limit->reason,
            ] : ['daily_limit' => 200, 'is_enabled' => true, 'reason' => ''],
        ]);
    }

    /** Users — Live: table feed (top 20 by last_seen) */
    public function usersTable(Request $request)
    {
        $q = trim((string) $request->query('q', ''));
        $users = User::query()
            ->leftJoin('user_limits as ul', 'ul.user_id', '=', 'users.id')
            ->select([
                'users.id', 'users.name', 'users.email',
                'users.last_seen_at', 'users.last_ip', 'users.last_country',
                'users.is_banned',
                DB::raw('COALESCE(ul.daily_limit, 200) as daily_limit'),
                DB::raw('COALESCE(ul.is_enabled, 1) as is_enabled'),
            ])
            ->when($q !== '', function ($sql) use ($q) {
                $qq = "%{$q}%";
                $sql->where(function ($w) use ($qq) {
                    $w->where('users.email', 'like', $qq)
                      ->orWhere('users.name', 'like', $qq)
                      ->orWhere('users.last_ip', 'like', $qq);
                });
            })
            ->orderByDesc('users.last_seen_at')
            ->limit(20)
            ->get();

        $rows = $users->map(function ($u) {
            return [
                'id'       => $u->id,
                'name'     => $u->name,
                'email'    => $u->email,
                'last_seen'=> $u->last_seen_at ? Carbon::parse($u->last_seen_at)->diffForHumans() : '—',
                'ip'       => $u->last_ip,
                'country'  => $u->last_country,
                'limit'    => (int) $u->daily_limit,
                'enabled'  => (bool) $u->is_enabled,
                'banned'   => (bool) $u->is_banned,
            ];
        })->toArray();

        return response()->json(['rows' => $rows]);
    }

    /** Users — Live: sessions drawer (if table exists) */
    public function userSessions(User $user)
    {
        if (!Schema::hasTable('user_sessions')) {
            return response()->json(['rows' => [], 'history' => []]);
        }

        $rows = DB::table('user_sessions')
            ->where('user_id', $user->id)
            ->orderByDesc('login_at')
            ->limit(50)
            ->get(['login_at', 'logout_at', 'ip', 'country']);

        $out = $rows->map(fn ($r) => [
            'login_at'  => $r->login_at ? Carbon::parse($r->login_at)->toDateTimeString() : '—',
            'logout_at' => $r->logout_at ? Carbon::parse($r->logout_at)->toDateTimeString() : '—',
            'ip'        => $r->ip,
            'country'   => $r->country,
        ])->toArray();

        // Optional: quick per-user recent activity for the drawer, if analyze_logs exists
        $history = [];
        if (Schema::hasTable('analyze_logs')) {
            $history = DB::table('analyze_logs')
                ->where('user_id', $user->id)
                ->orderByDesc('created_at')
                ->limit(30)
                ->get(['created_at',
                       DB::raw("COALESCE(url, query, input, page_url) as display"),
                       DB::raw("COALESCE(tool, tool_name, section) as tool"),
                       DB::raw("COALESCE(tokens, total_tokens, 0) as tokens"),
                       DB::raw("COALESCE(cost, cost_usd, usd_cost, 0) as cost")])
                ->map(fn ($r) => [
                    'when' => Carbon::parse($r->created_at)->format('Y-m-d H:i'),
                    'display' => (string)($r->display ?? '—'),
                    'tool' => (string)($r->tool ?? '—'),
                    'tokens' => (int)($r->tokens ?? 0),
                    'cost' => number_format((float)($r->cost ?? 0), 4, '.', ''),
                ])->toArray();
        }

        return response()->json(['rows' => $out, 'history' => $history]);
    }

    // ===== Helpers =====

    private function kpis(): array
    {
        $now = Carbon::now();

        $totalUsers = (int) User::query()->count();

        $active5m = (int) User::query()
            ->whereNotNull('last_seen_at')
            ->where('last_seen_at', '>=', $now->clone()->subMinutes(5))
            ->count();

        $searchesToday = 0; $dau = 0; $mau = 0; $active24h = 0;
        if (Schema::hasTable('analyze_logs')) {
            $searchesToday = (int) DB::table('analyze_logs')
                ->whereDate('created_at', $now->toDateString())->count();

            $active24h = (int) DB::table('analyze_logs')
                ->where('created_at', '>=', $now->clone()->subDay())->count();

            $dau = (int) DB::table('analyze_logs')
                ->where('created_at', '>=', $now->clone()->subDay())
                ->distinct('user_id')->count('user_id');

            $mau = (int) DB::table('analyze_logs')
                ->where('created_at', '>=', $now->clone()->subDays(30))
                ->distinct('user_id')->count('user_id');
        }

        $cost24h = 0.0; $tokens24h = 0;
        if (Schema::hasTable('open_ai_usages')) {
            $q = DB::table('open_ai_usages')->where('created_at', '>=', $now->clone()->subDay());
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

        // DB ping
        try {
            $t0 = microtime(true);
            DB::select('SELECT 1');
            $lat = (int) round((microtime(true) - $t0) * 1000);
            $services[] = ['name' => 'Database', 'ok' => true, 'latency_ms' => $lat];
        } catch (\Throwable $e) {
            $services[] = ['name' => 'Database', 'ok' => false, 'latency_ms' => null];
        }

        // Queue backlog (optional)
        if (Schema::hasTable('jobs')) {
            try {
                $count = (int) DB::table('jobs')->count();
                $services[] = ['name' => 'Queue', 'ok' => true, 'latency_ms' => $count . ' pending'];
            } catch (\Throwable $e) {
                $services[] = ['name' => 'Queue', 'ok' => false, 'latency_ms' => null];
            }
        }

        // Scheduler heartbeat (set by console command)
        $beatAt = Cache::get('dash:heartbeat_at');
        if ($beatAt) {
            $diff = Carbon::parse($beatAt)->diffInSeconds(now());
            $services[] = ['name' => 'Scheduler', 'ok' => $diff <= 120, 'latency_ms' => $diff . 's'];
        } else {
            $services[] = ['name' => 'Scheduler', 'ok' => false, 'latency_ms' => null];
        }

        // OpenAI API heartbeat (recent usage)
        if (Schema::hasTable('open_ai_usages')) {
            $apiOk = DB::table('open_ai_usages')->where('created_at', '>=', now()->subHours(6))->exists();
            $services[] = ['name' => 'OpenAI API', 'ok' => (bool) $apiOk, 'latency_ms' => null];
        }

        return $services;
    }

    private function traffic30(): array
    {
        if (!Schema::hasTable('analyze_logs')) return [];

        $start = now()->startOfDay()->subDays(29);
        $raw = DB::table('analyze_logs')
            ->select(DB::raw('DATE(created_at) as day'), DB::raw('COUNT(*) as c'))
            ->where('created_at', '>=', $start)
            ->groupBy(DB::raw('DATE(created_at)'))
            ->orderBy('day', 'asc')
            ->pluck('c', 'day');

        $out = [];
        for ($d = $start->copy(); $d <= now()->startOfDay(); $d->addDay()) {
            $key = $d->toDateString();
            $out[] = ['day' => $key, 'count' => (int) ($raw[$key] ?? 0)];
        }
        return $out;
    }

    private function historyLatest(): array
    {
        if (!Schema::hasTable('analyze_logs')) return [];

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

        return $rows->map(fn ($r) => [
            'when'    => Carbon::parse($r->created_at)->format('Y-m-d H:i'),
            'user'    => $r->email ?? '—',
            'display' => (string) ($r->display ?? '—'),
            'tool'    => (string) ($r->tool ?? '—'),
            'tokens'  => (int) ($r->tokens ?? 0),
            'cost'    => number_format((float) ($r->cost ?? 0), 4, '.', ''),
        ])->toArray();
    }

    /** NEW: top queries over last 7 days (if your logs track queries) */
    private function topQueries7d(): array
    {
        if (!Schema::hasTable('analyze_logs')) return [];
        // try to aggregate on a generic "query" or "url"
        $rows = DB::table('analyze_logs')
            ->select(DB::raw("LOWER(TRIM(COALESCE(query, url, page_url, input))) as q"), DB::raw('COUNT(*) as c'))
            ->where('created_at', '>=', now()->subDays(7))
            ->whereNotNull(DB::raw("COALESCE(query, url, page_url, input)"))
            ->groupBy('q')
            ->orderByDesc('c')
            ->limit(15)
            ->get();
        return $rows->map(fn ($r) => ['query' => $r->q, 'count' => (int) $r->c])->toArray();
    }

    /** NEW: error digest (if a simple error_logs table exists; else empty) */
    private function errorDigest24h(): array
    {
        if (!Schema::hasTable('error_logs')) return [];
        $rows = DB::table('error_logs')
            ->select(DB::raw('message'), DB::raw('COUNT(*) as c'))
            ->where('created_at', '>=', now()->subDay())
            ->groupBy('message')
            ->orderByDesc('c')
            ->limit(15)
            ->get();
        return $rows->map(fn ($r) => ['message' => $r->message, 'count' => (int) $r->c])->toArray();
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
}
