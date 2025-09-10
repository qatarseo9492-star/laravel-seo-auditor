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
        $mrr  = null;

        if (Schema::hasTable('subscriptions')) {
            $sum = DB::table('subscriptions')->where('status', 'active')->sum('mrr_cents');
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
            $k['dailyLimit'] = (int) ($avg ?: 1000);
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

    /**
     * Show latest items from BOTH analyze_logs (primary) and recent analysis_cache (last 7 days),
     * then de-duplicate, sort (desc), cap 50.
     */
    private function recentHistory(): array
    {
        $out = [];

        // ---------- A) analyze_logs ----------
        if (Schema::hasTable('analyze_logs')) {
            $q = DB::table('analyze_logs as a');
            $select = ['a.id','a.created_at','a.tool','a.url'];
            if (Schema::hasColumn('analyze_logs','tokens_used')) $select[] = 'a.tokens_used';
            if (Schema::hasColumn('analyze_logs','tokens'))      $select[] = 'a.tokens';
            if (Schema::hasColumn('analyze_logs','cost_usd'))    $select[] = 'a.cost_usd';
            if (Schema::hasColumn('analyze_logs','cost'))        $select[] = 'a.cost';

            $hasUser = Schema::hasColumn('analyze_logs','user_id') && Schema::hasTable('users');
            if ($hasUser) {
                $q->leftJoin('users as u','u.id','=','a.user_id');
                $select[] = 'u.email'; $select[] = 'u.name';
            }

            $logs = $q->orderByDesc('a.id')->limit(100)->get($select);

            foreach ($logs as $r) {
                $out[] = [
                    'ts'      => $r->created_at,
                    'when'    => $this->fmt($r->created_at),
                    'user'    => $r->name ?? $r->email ?? '—',
                    'display' => $r->url ? ('Analyzed URL '.$this->shortUrl($r->url)) : 'Analysis',
                    'tool'    => $r->tool ?: 'semantic',
                    'tokens'  => $r->tokens_used ?? $r->tokens ?? '—',
                    'cost'    => number_format((float)($r->cost_usd ?? $r->cost ?? 0), 4),
                    '_k'      => 'log:' . ($r->created_at ?? '') . ':' . ($r->url ?? '') . ':' . ($r->tool ?? ''),
                ];
            }
        }

        // ---------- B) analysis_cache (recent 7 days) ----------
        if (Schema::hasTable('analysis_cache')) {
            $cols = DB::select('SHOW COLUMNS FROM analysis_cache');
            $has = function($name) use ($cols){ foreach ($cols as $c) if (($c->Field ?? null)===$name) return true; return false; };

            $need = ['id','created_at','url','tool'];
            foreach ($need as $n) { if (!$has($n)) return $this->dedupeSortCap($out); }

            $cacheQ = DB::table('analysis_cache')->where('created_at','>=',now()->subDays(7));
            $select = ['id','created_at','url','tool'];
            $hasUser = $has('user_id') && Schema::hasTable('users');
            if ($hasUser) $select[]='user_id';

            $cache = $cacheQ->orderByDesc('id')->limit(150)->get($select);

            $users = [];
            if ($hasUser && $cache->count()) {
                $uids = $cache->pluck('user_id')->filter()->unique()->values();
                if ($uids->count()) {
                    $users = DB::table('users')->whereIn('id',$uids)->get(['id','name','email'])->keyBy('id');
                }
            }

            foreach ($cache as $r) {
                $u = $hasUser ? ($users[$r->user_id] ?? null) : null;
                $out[] = [
                    'ts'      => $r->created_at,
                    'when'    => $this->fmt($r->created_at),
                    'user'    => $u ? (($u->name ?? null) ?: ($u->email ?? '—')) : '—',
                    'display' => $r->url ? ('Analyzed URL '.$this->shortUrl($r->url)) : 'Analysis',
                    'tool'    => $r->tool ?? 'semantic',
                    'tokens'  => '—',
                    'cost'    => number_format(0, 4),
                    '_k'      => 'cache:' . ($r->created_at ?? '') . ':' . ($r->url ?? '') . ':' . ($r->tool ?? 'semantic'),
                ];
            }
        }

        return $this->dedupeSortCap($out);
    }

    private function dedupeSortCap(array $rows): array
    {
        $seen = [];
        $merged = [];
        foreach ($rows as $row) {
            $k = $row['_k'] ?? ( ($row['ts'] ?? '') . ':' . ($row['display'] ?? '') . ':' . ($row['tool'] ?? '') );
            if (!isset($seen[$k])) {
                $seen[$k] = true;
                unset($row['_k']);
                $merged[] = $row;
            }
        }
        usort($merged, fn($a,$b) => strcmp(($b['ts'] ?? ''), ($a['ts'] ?? '')));
        return array_slice($merged, 0, 50);
    }

    private function trafficSeries(): array
    {
        try { if (Schema::hasTable('analyze_logs'))   return $this->seriesFromTable('analyze_logs'); } catch (\Throwable $e) {}
        try { if (Schema::hasTable('analysis_cache')) return $this->seriesFromTable('analysis_cache'); } catch (\Throwable $e) {}

        $usageTable = Schema::hasTable('open_ai_usages') ? 'open_ai_usages' : (Schema::hasTable('openai_usage') ? 'openai_usage' : null);
        if ($usageTable) { try { return $this->seriesFromTable($usageTable); } catch (\Throwable $e) {} }

        return [];
    }

    private function seriesFromTable(string $table): array
    {
        $days  = 14;
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

    public function usersTable(Request $request)
    {
        $q = trim((string) $request->query('q', ''));
        $rows = [];

        if (Schema::hasTable('users')) {
            $builder = DB::table('users');

            if ($q !== '') {
                $builder->where(function($w) use ($q) {
                    $w->where('email', 'like', "%{$q}%");
                    if (Schema::hasColumn('users','name'))         $w->orWhere('name', 'like', "%{$q}%");
                    if (Schema::hasColumn('users','last_ip'))      $w->orWhere('last_ip', 'like', "%{$q}%");
                    if (Schema::hasColumn('users','last_country')) $w->orWhere('last_country', 'like', "%{$q}%");
                });
            }

            if (Schema::hasColumn('users', 'last_seen_at')) $builder->orderByDesc('last_seen_at');
            else                                            $builder->orderByDesc('id');

            $users = $builder->limit(20)->get();

            $limits = [];
            if (Schema::hasTable('user_limits')) {
                $limits = DB::table('user_limits')->whereIn('user_id',$users->pluck('id'))->get()->keyBy('user_id');
            }

            foreach ($users as $u) {
                $limit = $limits[$u->id] ?? null;
                $rows[] = [
                    'id'        => $u->id,
                    'email'     => $u->email ?? '',
                    'name'      => $u->name  ?? '',
                    'enabled'   => (bool) ($limit->is_enabled ?? true),
                    'banned'    => (bool) ($u->is_banned ?? false),
                    'limit'     => (int)  ($limit->daily_limit ?? 1000),
                    'last_seen' => Schema::hasColumn('users','last_seen_at') ? ($u->last_seen_at ?? '—') : '—',
                    'ip'        => Schema::hasColumn('users','last_ip')      ? ($u->last_ip ?? '—')      : '—',
                    'country'   => Schema::hasColumn('users','last_country') ? ($u->last_country ?? '—') : '—',
                ];
            }
        }

        return response()->json(['rows' => $rows]);
    }

    public function userLive(User $user)
    {
        $limit = null;
        if (Schema::hasTable('user_limits')) {
            $limit = DB::table('user_limits')->where('user_id', $user->id)->first();
        }

        $latest = [];
        if (Schema::hasTable('analyze_logs')) {
            $select = ['created_at','tool','url'];
            if (Schema::hasColumn('analyze_logs','tokens_used')) $select[]='tokens_used';
            if (Schema::hasColumn('analyze_logs','tokens'))      $select[]='tokens';

            $rows = DB::table('analyze_logs')
                ->where('user_id', $user->id)
                ->orderByDesc('id')
                ->limit(20)
                ->get($select);

            foreach ($rows as $r) {
                $latest[] = [
                    'created_at' => $this->fmt($r->created_at),
                    'type'       => $r->url ? ('Analyzed URL '.$this->shortUrl($r->url)) : 'Analysis',
                    'tool'       => $r->tool ?: 'semantic',
                    'tokens'     => $r->tokens_used ?? $r->tokens ?? null,
                ];
            }
        }

        return response()->json([
            'user'  => [
                'id'           => $user->id,
                'email'        => $user->email ?? '',
                'name'         => $user->name ?? '',
                'last_seen_at' => Schema::hasColumn('users','last_seen_at') ? $user->last_seen_at : null,
                'last_ip'      => Schema::hasColumn('users','last_ip')      ? $user->last_ip      : null,
            ],
            'limit' => [
                'daily_limit' => (int)  ($limit->daily_limit ?? 1000),
                'is_enabled'  => (bool) ($limit->is_enabled  ?? true),
                'reason'      =>        ($limit->reason      ?? null),
            ],
            'latest' => $latest,
        ]);
    }

    public function userSessions(User $user)
    {
        if (!Schema::hasTable('user_sessions')) return response()->json(['rows' => []]);

        $rows = DB::table('user_sessions')
            ->where('user_id', $user->id)
            ->orderByDesc('login_at')
            ->limit(50)
            ->get(['login_at','logout_at','ip','country']);

        $out = [];
        foreach ($rows as $r) {
            $out[] = [
                'login_at'  => $this->fmt($r->login_at),
                'logout_at' => $this->fmt($r->logout_at),
                'ip'        => $r->ip ?? '—',
                'country'   => $r->country ?? '—',
            ];
        }
        return response()->json(['rows' => $out]);
    }

    private function shortUrl(?string $url): string
    {
        if (!$url) return '';
        try {
            $p = parse_url($url);
            $host = $p['host'] ?? '';
            $path = isset($p['path']) ? rtrim($p['path'], '/') : '';
            if (strlen($path) > 48) {
                $path = function_exists('mb_strimwidth') ? mb_strimwidth($path, 0, 48, '…') : substr($path, 0, 48).'…';
            }
            return $host.$path;
        } catch (\Throwable $e) { return $url; }
    }

    private function fmt($ts): ?string
    {
        if (!$ts) return null;
        try {
            if ($ts instanceof \DateTimeInterface) return Carbon::instance($ts)->format('Y-m-d H:i');
            if (is_numeric($ts) && strlen((string) $ts) <= 10) return Carbon::createFromTimestamp((int)$ts)->format('Y-m-d H:i');
            return Carbon::parse((string) $ts)->format('Y-m-d H:i');
        } catch (\Throwable $e) { return is_string($ts) ? $ts : null; }
    }
}
