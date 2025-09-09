<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
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
            $mrr = '$' . number_format((float)$sum / 100, 2);
        }

        return view('admin.dashboard', [
            'kpis' => $kpis,
            'mrr'  => $mrr,
        ]);
    }

    public function live(Request $request)
    {
        return response()->json([
            'kpis'     => $this->computeKpis(),
            'services' => $this->serviceHealth(),
            'history'  => $this->recentHistory(),
            'traffic'  => $this->trafficSeries(),
        ]);
    }

    private function computeKpis(): array
    {
        $today = now()->startOfDay();
        $k = [
            'searchesToday' => 0,
            'totalUsers'    => 0,
            'cost24h'       => 0.0,
            'dau'           => 0,
            'mau'           => 0,
            'active5m'      => 0,
            'dailyLimit'    => 100,
            // optional (filled if column exists)
            'tokens24h'     => 0,
        ];

        if (Schema::hasTable('users')) {
            $k['totalUsers'] = (int) User::count();
            if (Schema::hasColumn('users', 'last_seen_at')) {
                $k['active5m'] = (int) User::where('last_seen_at', '>=', now()->subMinutes(5))->count();
            }
        }

        if (Schema::hasTable('analyze_logs')) {
            $k['searchesToday'] = (int) DB::table('analyze_logs')
                ->where('created_at', '>=', $today)
                ->count();

            $k['dau'] = (int) DB::table('analyze_logs')
                ->where('created_at', '>=', now()->subDay())
                ->distinct('user_id')->count('user_id');

            $k['mau'] = (int) DB::table('analyze_logs')
                ->where('created_at', '>=', now()->subDays(30))
                ->distinct('user_id')->count('user_id');
        }

        if (Schema::hasTable('openai_usage')) {
            $k['cost24h'] = (float) DB::table('openai_usage')
                ->where('created_at', '>=', now()->subDay())
                ->sum('cost_usd');

            if (Schema::hasColumn('openai_usage', 'tokens')) {
                $k['tokens24h'] = (int) DB::table('openai_usage')
                    ->where('created_at', '>=', now()->subDay())
                    ->sum('tokens');
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
            $out[] = [
                'name' => 'Database',
                'ok' => true,
                'latency_ms' => (int) ((microtime(true) - $dbStart) * 1000),
            ];
        } catch (\Throwable $e) {
            $out[] = ['name' => 'Database', 'ok' => false, 'latency_ms' => null];
        }

        $okQueue = Schema::hasTable('jobs') && Schema::hasTable('failed_jobs');
        $out[] = ['name' => 'Queue', 'ok' => $okQueue, 'latency_ms' => null];

        $okOpenAI = Schema::hasTable('openai_usage');
        $out[] = ['name' => 'OpenAI', 'ok' => $okOpenAI, 'latency_ms' => null];

        return $out;
    }

    private function recentHistory(): array
    {
        // 1) Primary: analyze_logs (preferred)
        if (Schema::hasTable('analyze_logs')) {
            $rows = DB::table('analyze_logs as a')
                ->leftJoin('users as u', 'u.id', '=', 'a.user_id')
                ->orderByDesc('a.id')
                ->limit(50)
                ->get(['a.created_at','u.email','u.name','a.keyword','a.tool','a.tokens','a.cost_usd']);

            if ($rows->count()) {
                return $rows->map(function ($r) {
                    return [
                        'when'   => optional($r->created_at)->format('Y-m-d H:i'),
                        'user'   => $r->name ?: ($r->email ?? '—'),
                        'display'=> $r->keyword ? ('Analyzed "' . $r->keyword . '"') : 'Analysis',
                        'tool'   => $r->tool ?: 'semantic',
                        'tokens' => $r->tokens,
                        'cost'   => number_format((float)($r->cost_usd ?? 0), 4),
                    ];
                })->all();
            }
        }

        // 2) Fallback: user_sessions (login activity)
        if (Schema::hasTable('user_sessions')) {
            $rows = DB::table('user_sessions as s')
                ->leftJoin('users as u', 'u.id', '=', 's.user_id')
                ->orderByDesc(DB::raw('COALESCE(s.updated_at, s.login_at)'))
                ->limit(50)
                ->get(['s.login_at','s.updated_at','u.email','u.name','s.ip','s.country']);

            if ($rows->count()) {
                return $rows->map(function ($r) {
                    $when = $r->updated_at ?? $r->login_at;
                    return [
                        'when'   => optional($when)->format('Y-m-d H:i'),
                        'user'   => $r->name ?: ($r->email ?? '—'),
                        'display'=> 'Login' . ($r->ip ? (' from ' . $r->ip . ($r->country ? ' · ' . $r->country : '')) : ''),
                        'tool'   => 'auth',
                        'tokens' => '—',
                        'cost'   => '0.0000',
                    ];
                })->all();
            }
        }

        // 3) Last resort: recent signups
        if (Schema::hasTable('users')) {
            $rows = DB::table('users')
                ->orderByDesc('created_at')
                ->limit(50)
                ->get(['name','email','created_at']);

            if ($rows->count()) {
                return $rows->map(function ($r) {
                    return [
                        'when'   => optional($r->created_at)->format('Y-m-d H:i'),
                        'user'   => $r->name ?: ($r->email ?? '—'),
                        'display'=> 'Signup',
                        'tool'   => 'onboarding',
                        'tokens' => '—',
                        'cost'   => '0.0000',
                    ];
                })->all();
            }
        }

        return [];
    }

    private function trafficSeries(): array
    {
        if (!Schema::hasTable('analyze_logs')) {
            return [];
        }

        $days = 14;
        $start = now()->subDays($days - 1)->startOfDay();

        $raw = DB::table('analyze_logs')
            ->select(DB::raw('DATE(created_at) as d'), DB::raw('COUNT(*) as c'))
            ->where('created_at', '>=', $start)
            ->groupBy('d')
            ->orderBy('d')
            ->get();

        $map = $raw->keyBy('d');

        $out = [];
        for ($i = 0; $i < $days; $i++) {
            $d = $start->copy()->addDays($i)->toDateString();
            $out[] = [
                'day' => $d,
                'count' => (int) (($map[$d]->c ?? 0)),
            ];
        }

        return $out;
    }
}
