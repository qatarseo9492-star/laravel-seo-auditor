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

        // Analyze logs preferred
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
        // Fallback when activity is stored in analysis_cache
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

        // OpenAI usage (cost/tokens)
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

    private function recentHistory(): array
    {
        $events = [];

        /* 1) analyze_logs (if present) */
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

        /* 2) analysis_cache — tolerant extractor */
        if (Schema::hasTable('analysis_cache')) {
            // grab all columns so we can inspect dynamically
            $rows = DB::table('analysis_cache as c')
                ->leftJoin('users as u', 'u.id', '=', 'c.user_id')
                ->orderByDesc('c.id')
                ->limit(100)
                ->get(['c.*','u.email','u.name']);

            foreach ($rows as $r) {
                $ts   = $this->prop($r,'created_at') ?: $this->prop($r,'updated_at') ?: null;
                $tool = $this->firstNonEmpty([
                    $this->prop($r,'tool'),
                    $this->prop($r,'type'),
                    $this->prop($r,'mode'),
                    'semantic'
                ]);

                // tolerant URL/keyword discovery from columns and JSON blobs
                [$url, $kw, $title] = $this->extractFromCacheRow($r);

                $display = $url
                    ? ('Analyzed URL '.$this->shortUrl($url))
                    : ($kw ? 'Analyzed "'.$kw.'"' : ($title ?: 'Analysis'));

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
            $rows = DB::table('topic_analyses as t')
                ->leftJoin('users as u','u.id','=','t.user_id')
                ->orderByDesc('t.id')->limit(100)
                ->get(['t.*','u.email','u.name']);

            foreach ($rows as $r) {
                $ts  = $this->prop($r,'created_at') ?: $this->prop($r,'updated_at');
                $url = $this->firstNonEmpty([$this->prop($r,'url'), $this->prop($r,'page_url')]);
                $kw  = $this->firstNonEmpty([$this->prop($r,'keyword'), $this->prop($r,'query'), $this->prop($r,'term')]);

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
            $rows = DB::table('user_sessions as s')
                ->leftJoin('users as u', 'u.id', '=', 's.user_id')
                ->orderByDesc(DB::raw('COALESCE(s.updated_at, s.login_at, s.created_at)'))
                ->limit(100)
                ->get(['s.*','u.email','u.name']);

            foreach ($rows as $r) {
                $ts = $this->firstNonNull([$this->prop($r,'updated_at'), $this->prop($r,'login_at'), $this->prop($r,'created_at')]);
                $ip = $this->firstNonEmpty([$this->prop($r,'ip'), $this->prop($r,'ip_address')]);
                $country = $this->firstNonEmpty([$this->prop($r,'country'), $this->prop($r,'country_code')]);

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

        /* 6) Signups */
        if (Schema::hasTable('users')) {
            $rows = DB::table('users as u')->orderByRaw('COALESCE(u.last_seen_at, u.updated_at, u.created_at) DESC')->limit(100)->get(['u.*']);
            foreach ($rows as $r) {
                $ts = $this->firstNonNull([$this->prop($r,'last_seen_at'), $this->prop($r,'updated_at'), $this->prop($r,'created_at')]);
                $events[] = [
                    'ts'      => $ts,
                    'when'    => $this->fmt($ts),
                    'user'    => $this->prop($r,'name') ?: ($this->prop($r,'email') ?: '—'),
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

    private function extractFromCacheRow(object $r): array
    {
        // URL from many possible columns
        $url = $this->firstNonEmpty([
            $this->prop($r,'url'),
            $this->prop($r,'page_url'),
            $this->prop($r,'target_url'),
            $this->prop($r,'input_url'),
            $this->prop($r,'request_url'),
            $this->prop($r,'source_url'),
            $this->prop($r,'link'),
        ]);

        // Keyword / query
        $kw = $this->firstNonEmpty([
            $this->prop($r,'keyword'),
            $this->prop($r,'query'),
            $this->prop($r,'term'),
            $this->prop($r,'topic'),
            $this->prop($r,'search'),
        ]);

        $title = $this->firstNonEmpty([
            $this->prop($r,'title'),
            $this->prop($r,'page_title'),
        ]);

        // If still empty, scan common JSON blob columns.
        foreach (['payload','data','request','inputs','meta','json','result','response'] as $col) {
            $raw = $this->prop($r, $col);
            if (!$raw) continue;

            $arr = $this->safeJson($raw);
            if (!is_array($arr)) continue;

            $url = $url ?: $this->firstNonEmpty([
                $arr['url'] ?? null,
                $arr['page_url'] ?? null,
                $arr['target_url'] ?? null,
                $arr['input_url'] ?? null,
                $arr['request_url'] ?? null,
                $arr['source_url'] ?? null,
            ]);

            $kw = $kw ?: $this->firstNonEmpty([
                $arr['keyword'] ?? null,
                $arr['query'] ?? null,
                $arr['term'] ?? null,
                $arr['topic'] ?? null,
                $arr['search'] ?? null,
            ]);

            $title = $title ?: $this->firstNonEmpty([
                $arr['title'] ?? null,
                $arr['page_title'] ?? null,
            ]);

            if ($url && $kw && $title) break;
        }

        return [$url, $kw, $title];
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

    private function firstNonEmpty(array $vals)
    {
        foreach ($vals as $v) {
            if (is_string($v) && trim($v) !== '') return $v;
            if (!is_string($v) && !empty($v)) return $v;
        }
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

    private function safeJson($val)
    {
        if (is_array($val)) return $val;
        if (!is_string($val) || $val === '') return null;
        try { $d = json_decode($val, true, 512, JSON_THROW_ON_ERROR); return is_array($d) ? $d : null; }
        catch (\Throwable $e) { return null; }
    }
}
