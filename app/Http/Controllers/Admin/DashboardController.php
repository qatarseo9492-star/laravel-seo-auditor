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
        if (!Schema::hasTable('analyze_logs')) {
            return [];
        }

        $rows = DB::table('analyze_logs as a')
            ->leftJoin('users as u', 'u.id', '=', 'a.user_id')
            ->orderByDesc('a.id')
            ->limit(50)
            ->get(['a.created_at','u.email','u.name','a.keyword','a.tool','a.tokens','a.cost_usd']);

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
