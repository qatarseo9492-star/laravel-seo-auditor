<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Carbon;
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
        try { $kpis = $this->computeKpis(); }     catch (\Throwable $e) { $kpis = []; }
        try { $services = $this->serviceHealth(); } catch (\Throwable $e) { $services = []; }
        try { $history = $this->recentHistory(); }  catch (\Throwable $e) { $history = []; }
        try { $traffic = $this->trafficSeries(); }  catch (\Throwable $e) { $traffic = []; }

        return response()->json(compact('kpis','services','history','traffic'));
    }

    /* ---------------- KPIs ---------------- */

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

        // Users
        if (Schema::hasTable('users')) {
            $k['totalUsers'] = (int) User::count();
            if (Schema::hasColumn('users', 'last_seen_at')) {
                $k['active5m'] = (int) User::where('last_seen_at', '>=', now()->subMinutes(5))->count();
            }
        }

        // Analyze logs (primary preference if present)
        if (Schema::hasTable('analyze_logs')) {
            $k['searchesToday'] = (int) DB::table('analyze_logs')
                ->where('created_at', '>=', $today)->count();

            $k['dau'] = (int) DB::table('analyze_logs')
                ->where('created_at', '>=', now()->subDay())
                ->distinct('user_id')->count('user_id');

            $k['mau'] = (int) DB::table('analyze_logs')
                ->where('created_at', '>=', now()->subDays(30))
                ->distinct('user_id')->count('user_id');
        }
        // Fallback for “activity” if your app writes to analysis_cache instead
        elseif (Schema::hasTable('analysis_cache')) {
            $k['searchesToday'] = (int) DB::table('analysis_cache')
                ->where('created_at', '>=', $today)->count();

            $k['dau'] = (int) DB::table('analysis_cache')
                ->where('created_at', '>=', now()->subDay())
                ->distinct('user_id')->count('user_id');

            $k['mau'] = (int) DB::table('analysis_cache')
                ->where('created_at', '>=', now()->subDays(30))
                ->distinct('user_id')->count('user_id');
        }

        // OpenAI usage (cost/tokens) — support both table names + columns
        $usageTable = Schema::hasTable('open_ai_usages')
            ? 'open_ai_usages'
            : (Schema::hasTable('openai_usage') ? 'openai_usage' : null);

        if ($usageTable) {
            if (Schema::hasColumn($usageTable, 'cost_usd')) {
                $k['cost24h'] = (float) DB::table($usageTable)
                    ->where('created_at', '>=', now()->subDay())
                    ->sum('cost_usd');
            } elseif (Schema::hasColumn($usageTable, 'cost')) {
                $k['cost24h'] = (float) DB::table($usageTable)
                    ->where('created_at', '>=', now()->subDay())
                    ->sum('cost');
            }

            if (Schema::hasColumn($usageTable, 'tokens')) {
                $k['tokens24h'] = (int) DB::table($usageTable)
                    ->where('created_at', '>=', now()->subDay())
                    ->sum('tokens');
            }
        }

        // Daily limit avg
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

        $out[] = ['name' => 'Queue', 'ok' => Schema::hasTable('jobs') && Schema::hasTable('failed_jobs'), 'latency_ms' => null];
        $out[] = ['name' => 'OpenAI', 'ok' => Schema::hasTable('open_ai_usages') || Schema::hasTable('openai_usage'), 'latency_ms' => null];

        return $out;
    }

    /* ---------------- History ---------------- */

    /**
     * Compose a unified, robust feed from:
     * - analyze_logs (if present)
     * - analysis_cache (fallback used by your analyzer UI)
     * - topic_analyses (optional)
     * - open_ai_usages / openai_usage (LLM calls)
     * - user_sessions (logins)
     * - users (signups) as a last resort
     */
    private function recentHistory(): array
    {
        $events = [];

        /* 1) analyze_logs (primary) */
        if (Schema::hasTable('analyze_logs')) {
            $sel = ['a.created_at'];
            $has = fn(string $c) => Schema::hasColumn('analyze_logs', $c);

            if ($has('keyword'))   $sel[] = DB::raw('a.keyword as kw');
            if ($has('query'))     $sel[] = DB::raw('a.query as qry');
            if ($has('prompt'))    $sel[] = DB::raw('a.prompt as prm');
            if ($has('url'))       $sel[] = DB::raw('a.url as url');
            if ($has('type'))      $sel[] = DB::raw('a.type as type');
            if ($has('tool'))      $sel[] = 'a.tool';
            if ($has('tokens'))    $sel[] = 'a.tokens';
            if ($has('cost_usd'))  $sel[] = 'a.cost_usd';
            if (!$has('cost_usd') && $has('cost')) $sel[] = DB::raw('a.cost as cost');

            $rows = DB::table('analyze_logs as a')
                ->leftJoin('users as u', 'u.id', '=', 'a.user_id')
                ->orderByDesc('a.id')
                ->limit(100)
                ->get(array_merge($sel, ['u.email','u.name']));

            foreach ($rows as $r) {
                $events[] = $this->normEventFromAnalyze($r);
            }
        }

        /* 2) analysis_cache — where your analyzer stores URL analyses */
        if (Schema::hasTable('analysis_cache')) {
            $sel = ['c.created_at'];
            $has = fn(string $c) => Schema::hasColumn('analysis_cache', $c);

            if ($has('url'))       $sel[] = 'c.url';
            if ($has('keyword'))   $sel[] = 'c.keyword';
            if ($has('query'))     $sel[] = 'c.query';
            if ($has('title'))     $sel[] = 'c.title';
            if ($has('type'))      $sel[] = 'c.type';
            if ($has('tool'))      $sel[] = 'c.tool';
            if ($has('user_id'))   $sel[] = 'c.user_id';

            $q = DB::table('analysis_cache as c');
            if ($has('user_id')) {
                $q->leftJoin('users as u','u.id','=','c.user_id');
                $userSel = ['u.email','u.name'];
            } else {
                $userSel = [];
            }

            $rows = $q->orderByDesc('c.id')
                      ->limit(100)
                      ->get(array_merge($sel, $userSel));

            foreach ($rows as $r) {
                $ts = $r->created_at ?? null;
                $url = $this->prop($r,'url');
                $kw  = $this->prop($r,'keyword') ?: $this->prop($r,'query');
                $ttl = $this->prop($r,'title');

                $display = $url ? ('Analyzed URL '.$this->shortUrl($url)) : ($kw ? 'Analyzed "'.$kw.'"' : ($ttl ?: 'Analysis'));
                $tool    = $this->prop($r,'tool') ?: ($this->prop($r,'type') ?: 'semantic');

                $events[] = [
                    'ts'      => $ts,
                    'when'    => $this->fmt($ts),
                    'user'    => $this->prop($r,'name') ?: ($this->prop($r,'email') ?: '—'),
                    'display' => $display,
                    'tool'    => $tool,
                    'tokens'  => '—',
                    'cost'    => '0.0000',
                ];
            }
        }

        /* 3) topic_analyses (optional) */
        if (Schema::hasTable('topic_analyses')) {
            $sel = ['t.created_at'];
            $has = fn(string $c) => Schema::hasColumn('topic_analyses', $c);

            if ($has('url'))      $sel[] = 't.url';
            if ($has('keyword'))  $sel[] = 't.keyword';
            if ($has('user_id'))  $sel[] = 't.user_id';

            $q = DB::table('topic_analyses as t');
            if ($has('user_id')) {
                $q->leftJoin('users as u','u.id','=','t.user_id');
                $userSel = ['u.email','u.name'];
            } else {
                $userSel = [];
            }

            $rows = $q->orderByDesc('t.id')->limit(100)->get(array_merge($sel, $userSel));

            foreach ($rows as $r) {
                $ts = $r->created_at ?? null;
                $url = $this->prop($r,'url');
                $kw  = $this->prop($r,'keyword');

                $display = $url ? ('Topic analysis '.$this->shortUrl($url)) : ($kw ? 'Topic analysis "'.$kw.'"' : 'Topic analysis');

                $events[] = [
                    'ts'      => $ts,
                    'when'    => $this->fmt($ts),
                    'user'    => $this->prop($r,'name') ?: ($this->prop($r,'email') ?: '—'),
                    'display' => $display,
                    'tool'    => 'topic_cluster',
                    'tokens'  => '—',
                    'cost'    => '0.0000',
                ];
            }
        }

        /* 4) OpenAI usage */
        $usageTable = Schema::hasTable('open_ai_usages')
            ? 'open_ai_usages'
            : (Schema::hasTable('openai_usage') ? 'openai_usage' : null);

        if ($usageTable) {
            $hasUserId  = Schema::hasColumn($usageTable, 'user_id');
            $hasModel   = Schema::hasColumn($usageTable, 'model');
            $hasPath    = Schema::hasColumn($usageTable, 'path');
            $hasTokens  = Schema::hasColumn($usageTable, 'tokens');
            $hasCostUsd = Schema::hasColumn($usageTable, 'cost_usd');
            $hasCost    = Schema::hasColumn($usageTable, 'cost');

            $q = DB::table("$usageTable as x");
            if ($hasUserId) { $q->leftJoin('users as u','u.id','=','x.user_id')->addSelect('u.email','u.name'); }
            $q->orderByDesc('x.id')->limit(100);
            $select = ['x.created_at'];
            if ($hasModel)   $select[] = 'x.model';
            if ($hasPath)    $select[] = 'x.path';
            if ($hasTokens)  $select[] = 'x.tokens';
            if ($hasCostUsd) $select[] = 'x.cost_usd';
            if ($hasCost)    $select[] = 'x.cost';

            $rows = $q->get($select);
            foreach ($rows as $r) {
                $ts = $r->created_at ?? null;
                $model = $this->prop($r,'model');
                $path  = $this->prop($r,'path');
                $disp  = $model && $path ? "$model — $path" : ($model ?: ($path ?: 'LLM call'));

                $events[] = [
                    'ts'      => $ts,
                    'when'    => $this->fmt($ts),
                    'user'    => $this->prop($r,'name') ?: ($this->prop($r,'email') ?: '—'),
                    'display' => $disp,
                    'tool'    => 'openai',
                    'tokens'  => $this->prop($r,'tokens') ?? '—',
                    'cost'    => number_format((float)($this->prop($r,'cost_usd') ?? $this->prop($r,'cost') ?? 0), 4),
                ];
            }
        }

        /* 5) Logins */
        if (Schema::hasTable('user_sessions')) {
            $cols = $this->existingColumns('user_sessions', ['updated_at','login_at','created_at','ip','ip_address','country','country_code']);

            $select = [];
            if (isset($cols['updated_at'])) $select[] = 's.updated_at';
            if (isset($cols['login_at']))   $select[] = 's.login_at';
            if (isset($cols['created_at'])) $select[] = 's.created_at';
            if (isset($cols['ip']))         $select[] = 's.ip';
            if (isset($cols['ip_address'])) $select[] = DB::raw('s.ip_address as ip');
            if (isset($cols['country']))    $select[] = 's.country';
            if (isset($cols['country_code'])) $select[] = DB::raw('s.country_code as country');

            $rows = DB::table('user_sessions as s')
                ->leftJoin('users as u', 'u.id', '=', 's.user_id')
                ->orderByDesc(DB::raw('COALESCE(s.updated_at, s.login_at, s.created_at)'))
                ->limit(100)
                ->get(array_merge($select, ['u.email','u.name']));

            foreach ($rows as $r) {
                $ts = $this->firstNonNull([$this->prop($r,'updated_at'), $this->prop($r,'login_at'), $this->prop($r,'created_at')]);
                $ip = $this->prop($r,'ip');
                $country = $this->prop($r,'country');

                $events[] = [
                    'ts'      => $ts,
                    'when'    => $this->fmt($ts),
                    'user'    => $this->prop($r,'name') ?: ($this->prop($r,'email') ?: '—'),
                    'display' => 'Login'.($ip ? (' from '.$ip.($country ? ' · '.$country : '')) : ''),
                    'tool'    => 'auth',
                    'tokens'  => '—',
                    'cost'    => '0.0000',
                ];
            }
        }

        /* 6) Signups (last resort) */
        if (Schema::hasTable('users')) {
            $hasCreated = Schema::hasColumn('users', 'created_at');
            $hasUpdated = Schema::hasColumn('users', 'updated_at');
            $hasSeen    = Schema::hasColumn('users', 'last_seen_at');

            $select = ['u.name','u.email','u.id'];
            $coalesce = [];
            if ($hasSeen)    { $select[] = 'u.last_seen_at'; $coalesce[] = 'u.last_seen_at'; }
            if ($hasUpdated) { $select[] = 'u.updated_at';   $coalesce[] = 'u.updated_at'; }
            if ($hasCreated) { $select[] = 'u.created_at';   $coalesce[] = 'u.created_at'; }

            $order = $coalesce ? 'COALESCE('.implode(',', $coalesce).') DESC' : 'u.id DESC';

            $rows = DB::table('users as u')->select($select)->orderByRaw($order)->limit(100)->get();

            foreach ($rows as $r) {
                $ts = $this->firstNonNull([$this->prop($r,'last_seen_at'), $this->prop($r,'updated_at'), $this->prop($r,'created_at')]);
                $events[] = [
                    'ts'      => $ts,
                    'when'    => $this->fmt($ts),
                    'user'    => $r->name ?: ($r->email ?? '—'),
                    'display' => 'Signup',
                    'tool'    => 'onboarding',
                    'tokens'  => '—',
                    'cost'    => '0.0000',
                ];
            }
        }

        // Sort + limit 50
        usort($events, fn($a,$b) => $this->tsToSortable($b['ts']) <=> $this->tsToSortable($a['ts']));
        return array_slice($events, 0, 50);
    }

    /* ---------------- Traffic ---------------- */

    private function trafficSeries(): array
    {
        // Prefer analyze_logs; else analysis_cache; else OpenAI usage
        if (Schema::hasTable('analyze_logs'))   return $this->seriesFromTable('analyze_logs');
        if (Schema::hasTable('analysis_cache')) return $this->seriesFromTable('analysis_cache');

        $usageTable = Schema::hasTable('open_ai_usages')
            ? 'open_ai_usages'
            : (Schema::hasTable('openai_usage') ? 'openai_usage' : null);
        if ($usageTable) return $this->seriesFromTable($usageTable);

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

    /* ---------------- helpers ---------------- */

    private function normEventFromAnalyze($r): array
    {
        $ts = $r->created_at ?? null;

        $kw  = $this->prop($r,'kw');
        $qry = $this->prop($r,'qry');
        $prm = $this->prop($r,'prm');
        $url = $this->prop($r,'url');
        $typ = $this->prop($r,'type');

        $display = $url ? ('Analyzed URL '.$this->shortUrl($url))
                  : ($kw ? 'Analyzed "'.$kw.'"'
                  : ($qry ? 'Query "'.$qry.'"'
                  : ($prm ? 'Prompt "'.mb_strimwidth($prm,0,40,'…').'"'
                  : ($typ ?: 'Analysis'))));

        $tool   = $this->prop($r,'tool') ?: 'semantic';
        $tokens = $this->prop($r,'tokens') ?? '—';
        $cost   = $this->prop($r,'cost_usd') ?? $this->prop($r,'cost') ?? 0;

        return [
            'ts'      => $ts,
            'when'    => $this->fmt($ts),
            'user'    => $this->prop($r,'name') ?: ($this->prop($r,'email') ?: '—'),
            'display' => $display,
            'tool'    => $tool,
            'tokens'  => $tokens,
            'cost'    => number_format((float)($cost ?: 0), 4),
        ];
    }

    private function shortUrl(?string $url): string
    {
        if (!$url) return '';
        try {
            $p = parse_url($url);
            $host = $p['host'] ?? '';
            $path = isset($p['path']) ? rtrim($p['path'], '/') : '';
            if (strlen($path) > 32) $path = mb_strimwidth($path, 0, 32, '…');
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
            if (is_numeric($ts) && strlen((string) $ts) <= 10) return Carbon::createFromTimestamp((int) $ts)->format('Y-m-d H:i');
            return Carbon::parse((string) $ts)->format('Y-m-d H:i');
        } catch (\Throwable $e) {
            return is_string($ts) ? $ts : null;
        }
    }

    private function prop(object $o, string $key)
    {
        return property_exists($o, $key) ? $o->{$key} : null;
        }

    private function firstNonNull(array $vals)
    {
        foreach ($vals as $v) if (!is_null($v)) return $v;
        return null;
    }

    private function tsToSortable($ts): int
    {
        try {
            if ($ts instanceof \DateTimeInterface) return (int) Carbon::instance($ts)->timestamp;
            if (is_numeric($ts) && strlen((string) $ts) <= 10) return (int) $ts;
            return Carbon::parse((string) $ts)->timestamp;
        } catch (\Throwable $e) {
            return 0;
        }
    }

    private function existingColumns(string $table, array $candidates): array
    {
        $out = [];
        foreach ($candidates as $c) if (Schema::hasColumn($table, $c)) $out[$c] = true;
        return $out;
    }
}
