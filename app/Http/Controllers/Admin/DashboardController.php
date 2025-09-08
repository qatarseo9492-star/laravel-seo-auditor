<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\AnalyzeLog;
use App\Models\OpenAiUsage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // ----- your existing stats (unchanged) -----
        $totalUsers      = User::count();
        $searchesToday   = AnalyzeLog::whereDate('created_at', today())->count();
        $activeUsers     = AnalyzeLog::where('created_at', '>=', now()->subMinutes(5))
                               ->distinct('user_id')->count('user_id');
        $openAiCostToday = (float) OpenAiUsage::whereDate('created_at', today())->sum('cost');

        $avg7d     = round(AnalyzeLog::where('created_at', '>=', now()->subDays(7))->count() / 7, 1);
        $costMonth = (float) OpenAiUsage::whereBetween('created_at', [now()->startOfMonth(), now()])->sum('cost');

        $stats = [
            'searchesToday' => $searchesToday,
            'avg7d'         => $avg7d,
            'totalUsers'    => $totalUsers,
            'newUsers'      => User::whereDate('created_at', today())->count(),
            'costToday'     => $openAiCostToday,
            'costMonth'     => $costMonth,
            'active5m'      => $activeUsers,
            'peakToday'     => (int) AnalyzeLog::whereDate('created_at', today())
                                  ->selectRaw('HOUR(created_at) h, COUNT(*) c')
                                  ->groupBy('h')->orderByDesc('c')->value('c') ?? 0,
        ];

        $system = [
            'psi'    => (bool) (env('PAGESPEED_API_KEY') ?: config('services.psi.key')),
            'openai' => (bool) env('OPENAI_API_KEY'),
            'cache'  => app('cache')->getDefaultDriver() ? true : false,
        ];

        // ----- users (unchanged, if you already have paginate) -----
        $users = User::with('limit')->latest()->paginate(10);
        $users->getCollection()->transform(function ($u) {
            $u->today_count = AnalyzeLog::where('user_id', $u->id)
                              ->whereDate('created_at', today())->count();
            $u->month_count = AnalyzeLog::where('user_id', $u->id)
                              ->where('created_at', '>=', now()->startOfMonth())->count();
            $u->status = property_exists($u, 'banned') ? ($u->banned ? 'Banned' : 'Active') : 'Active';
            return $u;
        });

        // ===== FIX: build Top Items using the first existing column =====
        $logTable   = (new AnalyzeLog)->getTable();
        $candidates = ['query','keyword','search_term','url','target_url','page','path','route'];
        $nameCol    = collect($candidates)->first(fn($c) => Schema::hasColumn($logTable, $c));

        if ($nameCol) {
            $topItems = DB::table($logTable)
                ->select("$nameCol as name", DB::raw('COUNT(*) as count'))
                ->groupBy($nameCol)
                ->orderByDesc('count')
                ->limit(6)
                ->get()
                ->map(fn($r) => ['name' => (string)$r->name, 'count' => (int)$r->count])
                ->toArray();
        } else {
            $topItems = []; // nothing suitable on this table
        }

        // ===== OPTIONAL: add a safe display field for history rows =====
        $history = AnalyzeLog::with('user')->latest()->limit(80)->get();
        $history->transform(function ($h) use ($candidates) {
            foreach ($candidates as $c) {
                if (isset($h->$c) && !empty($h->$c)) { $h->display = $h->$c; return $h; }
            }
            $h->display = ''; return $h;
        });
        // === v3 additive data (read-only, safe) ===
        $services = [];
        try {
            $t = microtime(true);
            DB::select('select 1');
            $services[] = ['name' => 'DB', 'ok' => true, 'latency_ms' => round((microtime(true) - $t) * 1000)];
        } catch (\Throwable $e) {
            $services[] = ['name' => 'DB', 'ok' => false, 'latency_ms' => null];
        }

        if (Schema::hasTable('jobs')) {
            try {
                $backlog = DB::table('jobs')->count();
                $services[] = ['name' => 'Queue', 'ok' => $backlog < 1000, 'latency_ms' => $backlog]; // using backlog as metric
            } catch (\Throwable $e) {
                $services[] = ['name' => 'Queue', 'ok' => true, 'latency_ms' => null];
            }
        }

        // OpenAI API heartbeat (based on recent usage logs if table exists)
        $apiOk = true;
        try {
            if (Schema::hasTable((new \App\Models\OpenAiUsage)->getTable())) {
                $apiOk = OpenAiUsage::where('created_at', '>=', now()->subHours(6))->exists();
            }
        } catch (\Throwable $e) { $apiOk = true; }
        $services[] = ['name' => 'OpenAI API', 'ok' => (bool)$apiOk, 'latency_ms' => null];

        // User limits summary (only if table exists)
        $limitsSummary = ['enabled' => 0, 'disabled' => 0, 'default' => 200];
        if (Schema::hasTable('user_limits')) {
            try {
                $limitsSummary['enabled'] = (int) DB::table('user_limits')->where('is_enabled', 1)->count();
                $limitsSummary['disabled'] = (int) DB::table('user_limits')->where('is_enabled', 0)->count();
            } catch (\Throwable $e) {}
        }


        return view('admin.dashboard', compact(
            'totalUsers','searchesToday','activeUsers','openAiCostToday','users',
            'stats','system','history','topItems'
        ));
    }
}
