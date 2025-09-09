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
// Optional models (tables probed at runtime)
use App\Models\AnalyzeLog;
use App\Models\OpenAiUsage;

class DashboardController extends Controller
{
    /** Render the Blade */
    public function index()
    {
        $limitsSummary = $this->limitsSummary();
        return view('admin.dashboard', compact('limitsSummary'));
    }

    /** Live JSON feed (polled by UI every 10s). Robust & exception-safe. */
    public function live(Request $request)
    {
        $out = [
            'kpis'     => [],
            'services' => [],
            'traffic'  => [],
            'history'  => [],
            'top'      => [],
            'errors'   => [],
        ];
        $errs = [];

        try { $out['kpis']     = Cache::remember('dash:kpis', 8, fn()=> $this->kpis()); }
        catch (\Throwable $e) { $errs['kpis'] = $e->getMessage(); }

        try { $out['services'] = Cache::remember('dash:services', 20, fn()=> $this->services()); }
        catch (\Throwable $e) { $errs['services'] = $e->getMessage(); }

        try { $out['traffic']  = Cache::remember('dash:traffic', 30, fn()=> $this->traffic30()); }
        catch (\Throwable $e) { $errs['traffic'] = $e->getMessage(); }

        try { $out['history']  = Cache::remember('dash:history', 12, fn()=> $this->historyLatest()); }
        catch (\Throwable $e) { $errs['history'] = $e->getMessage(); }

        try { $out['top']      = Cache::remember('dash:top', 30, fn()=> $this->topQueries7d()); }
        catch (\Throwable $e) { $errs['top'] = $e->getMessage(); }

        try { $out['errors']   = Cache::remember('dash:err', 30, fn()=> $this->errorDigest24h()); }
        catch (\Throwable $e) { $errs['errors'] = $e->getMessage(); }

        if ($request->boolean('debug')) {
            $out['_debug'] = $errs; // helpful while wiring; harmless otherwise
        }
        return response()->json($out);
    }

    /** Drawer: basic profile + limit */
    public function userLive(User $user)
    {
        $limit = $user->limit()->first();
        return response()->json([
            'user'  => [
                'id'            => $user->id,
                'email'         => $user->email,
                'last_seen_at'  => optional($user->last_seen_at)->toDateTimeString(),
                'last_ip'       => $user->last_ip,
                'last_country'  => $user->last_country,
                'last_login_at' => optional($user->last_login_at ?? null)->toDateTimeString(),
                'last_logout_at'=> optional($user->last_logout_at ?? null)->toDateTimeString(),
            ],
            'limit' => $limit ? [
                'daily_limit' => (int) $limit->daily_limit,
                'is_enabled'  => (bool) $limit->is_enabled,
                'reason'      => $limit->reason,
            ] : ['daily_limit' => 200, 'is_enabled' => true, 'reason' => ''],
        ]);
    }

    /** Users — Live table */
    public function usersTable(Request $request)
    {
        $q = trim((string) $request->get('q', ''));

        $users = User::query()
            ->when($q !== '', function ($qq) use ($q) {
                $qq->where(function ($w) use ($q) {
                    $w->where('email', 'like', "%{$q}%")
                      ->orWhere('name', 'like', "%{$q}%")
                      ->orWhere('last_ip', 'like', "%{$q}%")
                      ->orWhere('last_country', 'like', "%{$q}%");
                });
            })
            ->leftJoin('user_limits as ul', 'ul.user_id', '=', 'users.id')
            ->orderByDesc('users.last_seen_at')->orderBy('users.id')
            ->limit(20)
            ->get([
                'users.id','users.name','users.email','users.is_banned',
                'users.last_seen_at','users.last_ip','users.last_country',
                DB::raw('COALESCE(ul.daily_limit, 200) as daily_limit'),
                DB::raw('COALESCE(ul.is_enabled, 1) as is_enabled'),
            ]);

        $rows = $users->map(function ($u) {
            return [
                'id'       => (int) $u->id,
                'name'     => $u->name ?? '—',
                'email'    => $u->email,
                'banned'   => (bool) $u->is_banned,
                'enabled'  => (bool) $u->is_enabled,
                'limit'    => (int) $u->daily_limit,
                'last_seen'=> $u->last_seen_at ? Carbon::parse($u->last_seen_at)->diffForHumans() : '—',
                'ip'       => $u->last_ip,
                'country'  => $u->last_country,
            ];
        });

        return response()->json(['rows' => $rows], 200);
    }

    /** Drawer: sessions + recent activity for a user */
    public function userSessions(User $user)
    {
        $sessions = [];
        if (Schema::hasTable('user_sessions')) {
            $sessions = DB::table('user_sessions')
                ->where('user_id', $user->id)
                ->orderByDesc('login_at')
                ->limit(20)
                ->get(['login_at','logout_at','ip','country'])
                ->map(function ($s) {
                    return [
                        'login_at'  => $s->login_at ? Carbon::parse($s->login_at)->format('Y-m-d H:i') : '—',
                        'logout_at' => $s->logout_at ? Carbon::parse($s->logout_at)->format('Y-m-d H:i') : '—',
                        'ip'        => $s->ip,
                        'country'   => $s->country,
                    ];
                })->toArray();
        }

        $history = [];
        $histTable = $this->firstExistingTable(['analyze_logs', 'content_detections']);
        if ($histTable) {
            $cols = $this->cols($histTable);
            $displayCols = array_values(array_intersect(['url','query','input','page_url','target'], $cols));
            $toolCols    = array_values(array_intersect(['tool','tool_name','section','type'], $cols));
            $tokensCols  = array_values(array_intersect(['tokens','total_tokens'], $cols));
            $costCols    = array_values(array_intersect(['cost','cost_usd','usd_cost'], $cols));

            $display = $displayCols ? ('COALESCE('.implode(',', $displayCols).')') : "''";
            $tool    = $toolCols    ? ('COALESCE('.implode(',', $toolCols).')')    : "''";
            $tokens  = $tokensCols  ? ('COALESCE('.implode('+',$tokensCols).')')   : '0';
            $cost    = $costCols    ? ('COALESCE('.implode('+',$costCols).')')     : '0';

            $history = DB::table($histTable)
                ->where('user_id', $user->id)
                ->orderByDesc('created_at')
                ->limit(20)
                ->get([
                    'created_at',
                    DB::raw("$display as display"),
                    DB::raw("$tool as tool"),
                    DB::raw("$tokens as tokens"),
                    DB::raw("$cost as cost"),
                ])
                ->map(function ($r) {
                    return [
                        'when'    => Carbon::parse($r->created_at)->format('Y-m-d H:i'),
                        'display' => (string) ($r->display ?? '—'),
                        'tool'    => (string) ($r->tool ?? '—'),
                        'tokens'  => (int) ($r->tokens ?? 0),
                        'cost'    => number_format((float) ($r->cost ?? 0), 4, '.', ''),
                    ];
                })->toArray();
        }

        return response()->json(['rows' => $sessions, 'history' => $history], 200);
    }

    /* ===== Helpers ===== */

    private function kpis(): array
    {
        $now = Carbon::now();

        $totalUsers = (int) User::query()->count();

        $active5m = (int) User::query()
            ->whereNotNull('last_seen_at')
            ->where('last_seen_at', '>=', $now->clone()->subMinutes(5))
            ->count();

        $searchesToday = 0; $dau = 0; $mau = 0; $active24h = 0;
        $logTable = $this->firstExistingTable(['analyze_logs', 'content_detections']);
        if ($logTable) {
            $searchesToday = (int) DB::table($logTable)
                ->whereDate('created_at', $now->toDateString())->count();

            $dau = (int) DB::table($logTable)
                ->where('created_at', '>=', $now->clone()->subDay())
                ->distinct('user_id')->count('user_id');

            $mau = (int) DB::table($logTable)
                ->where('created_at', '>=', $now->clone()->subDays(30))
                ->distinct('user_id')->count('user_id');

            $active24h = (int) DB::table($logTable)
                ->where('created_at', '>=', $now->clone()->subDay())->count();
        }

        $cost24h = 0.0; $tokens24h = 0;
        $usageTable = $this->firstExistingTable(['open_ai_usages', 'openai_usages', 'ai_usages']);
        if ($usageTable) {
            $cols = $this->cols($usageTable);
            $hasTokens  = in_array('tokens', $cols, true);
            $hasPrompt  = in_array('prompt_tokens', $cols, true);
            $hasCompl   = in_array('completion_tokens', $cols, true);

            $hasCostUsd = in_array('cost_usd', $cols, true);
            $hasCost    = in_array('cost', $cols, true);
            $hasUsdCost = in_array('usd_cost', $cols, true);

            $q = DB::table($usageTable)->where('created_at', '>=', $now->clone()->subDay());

            if ($hasTokens) {
                $tokens24h = (int) $q->sum('tokens');
            } elseif ($hasPrompt || $hasCompl) {
                $pt = $hasPrompt ? 'prompt_tokens' : '0';
                $ct = $hasCompl  ? 'completion_tokens' : '0';
                $tokens24h = (int) DB::table($usageTable)
                    ->where('created_at', '>=', $now->clone()->subDay())
                    ->sum(DB::raw("$pt + $ct"));
            }

            if ($hasCostUsd)        $cost24h = (float) DB::table($usageTable)->where('created_at','>=',$now->clone()->subDay())->sum('cost_usd');
            elseif ($hasCost)       $cost24h = (float) DB::table($usageTable)->where('created_at','>=',$now->clone()->subDay())->sum('cost');
            elseif ($hasUsdCost)    $cost24h = (float) DB::table($usageTable)->where('created_at','>=',$now->clone()->subDay())->sum('usd_cost');
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

        try {
            $t0 = microtime(true);
            DB::select('SELECT 1');
            $lat = (int) round((microtime(true) - $t0) * 1000);
            $services[] = ['name' => 'Database', 'ok' => true, 'latency_ms' => $lat];
        } catch (\Throwable $e) {
            $services[] = ['name' => 'Database', 'ok' => false, 'latency_ms' => null];
        }

        if (Schema::hasTable('jobs')) {
            try {
                $count = (int) DB::table('jobs')->count();
                $services[] = ['name' => 'Queue', 'ok' => true, 'latency_ms' => $count.' pending'];
            } catch (\Throwable $e) {
                $services[] = ['name' => 'Queue', 'ok' => false, 'latency_ms' => null];
            }
        }

        $beatAt = Cache::get('dash:heartbeat_at');
        if ($beatAt) {
            $diff = Carbon::parse($beatAt)->diffInSeconds(now());
            $services[] = ['name' => 'Scheduler', 'ok' => $diff <= 120, 'latency_ms' => $diff.'s'];
        } else {
            $services[] = ['name' => 'Scheduler', 'ok' => false, 'latency_ms' => null];
        }

        if ($this->firstExistingTable(['open_ai_usages','openai_usages','ai_usages'])) {
            try {
                $apiOk = DB::table($this->firstExistingTable(['open_ai_usages','openai_usages','ai_usages']))
                    ->where('created_at', '>=', now()->subHours(6))->exists();
                $services[] = ['name' => 'OpenAI API', 'ok' => (bool) $apiOk, 'latency_ms' => null];
            } catch (\Throwable $e) {
                $services[] = ['name' => 'OpenAI API', 'ok' => false, 'latency_ms' => null];
            }
        }

        return $services;
    }

    private function traffic30(): array
    {
        $table = $this->firstExistingTable(['analyze_logs', 'content_detections']);
        if (!$table) return [];

        $start = now()->startOfDay()->subDays(29);
        $raw = DB::table($table)
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
        $table = $this->firstExistingTable(['analyze_logs', 'content_detections']);
        if (!$table) return [];

        $cols = $this->cols($table);
        $displayCols = array_values(array_intersect(['url','query','input','page_url','target'], $cols));
        $toolCols    = array_values(array_intersect(['tool','tool_name','section','type'], $cols));
        $tokensCols  = array_values(array_intersect(['tokens','total_tokens'], $cols));
        $costCols    = array_values(array_intersect(['cost','cost_usd','usd_cost'], $cols));

        $display = $displayCols ? ('COALESCE('.implode(',', $displayCols).')') : "''";
        $tool    = $toolCols    ? ('COALESCE('.implode(',', $toolCols).')')    : "''";
        $tokens  = $tokensCols  ? ('COALESCE('.implode('+',$tokensCols).')')   : '0';
        $cost    = $costCols    ? ('COALESCE('.implode('+',$costCols).')')     : '0';

        $rows = DB::table($table.' as a')
            ->leftJoin('users as u', 'u.id', '=', 'a.user_id')
            ->orderByDesc('a.created_at')
            ->limit(100)
            ->get([
                'a.created_at',
                'u.email',
                DB::raw("$display as display"),
                DB::raw("$tool as tool"),
                DB::raw("$tokens as tokens"),
                DB::raw("$cost as cost"),
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

    /** Top queries last 7 days (best-effort across schema variants) */
    private function topQueries7d(): array
    {
        $table = $this->firstExistingTable(['analyze_logs', 'content_detections']);
        if (!$table) return [];

        $cols = $this->cols($table);
        $candidate = null;
        foreach (['url','query','input','page_url','target'] as $c) {
            if (in_array($c, $cols, true)) { $candidate = $c; break; }
        }
        if (!$candidate) return [];

        return DB::table($table)
            ->select($candidate.' as item', DB::raw('COUNT(*) as c'))
            ->where('created_at', '>=', now()->subDays(7))
            ->groupBy($candidate)
            ->orderByDesc('c')
            ->limit(10)
            ->get()
            ->map(fn($r)=> ['query' => (string) $r->item, 'count' => (int) $r->c ])
            ->toArray();
    }

    /** Error digest (24h): prefers `failed_jobs`, otherwise empty */
    private function errorDigest24h(): array
    {
        if (!Schema::hasTable('failed_jobs')) return [];
        return DB::table('failed_jobs')
            ->where('failed_at', '>=', now()->subDay())
            ->select(DB::raw('SUBSTRING(error,1,120) as message'), DB::raw('COUNT(*) as c'))
            ->groupBy(DB::raw('SUBSTRING(error,1,120)'))
            ->orderByDesc('c')
            ->limit(10)
            ->get()
            ->map(fn($r)=> ['message' => (string) $r->message, 'count' => (int) $r->c ])
            ->toArray();
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

    /* ==== small utils ==== */

    private function cols(string $table): array
    {
        try { return array_map('strtolower', Schema::getColumnListing($table)); }
        catch (\Throwable $e) { return []; }
    }

    private function firstExistingTable(array $candidates): ?string
    {
        foreach ($candidates as $t) {
            if (Schema::hasTable($t)) return $t;
        }
        return null;
    }
}
