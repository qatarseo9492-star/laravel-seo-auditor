<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\AnalyzeLog;
use App\Models\OpenAiUsage;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // --- KPI Metrics ---
        $usersTotal = User::count();
        $activeUsers = User::whereNotNull('last_seen_at')
            ->where('last_seen_at', '>=', now()->subMinutes(30))
            ->count();
        $analyzeToday = AnalyzeLog::whereDate('created_at', today())->count();
        $analyzeMonth = AnalyzeLog::whereMonth('created_at', now()->month)->count();

        $metrics = [
            'users_total'   => $usersTotal,
            'active_users'  => $activeUsers,
            'analyze_today' => $analyzeToday,
            'analyze_month' => $analyzeMonth,
        ];

        // --- Daily usage (chart) ---
        $dailyUsage = AnalyzeLog::select(
                DB::raw('DATE(created_at) as day'),
                DB::raw('COUNT(*) as total')
            )
            ->where('created_at', '>=', now()->subDays(14))
            ->groupBy('day')
            ->orderBy('day')
            ->get();

        // --- Top active users ---
        $topUsers = User::select('id','name','email','last_ip','last_country','last_seen_at')
            ->withCount(['analyzeLogs' => function($q){
                $q->where('created_at','>=',now()->subDays(7));
            }])
            ->orderByDesc('analyze_logs_count')
            ->limit(10)
            ->get();

        // --- OpenAI usage ---
        $openaiUsage = OpenAiUsage::select(
                DB::raw('SUM(total_tokens) as tokens'),
                DB::raw('SUM(cost_usd) as cost')
            )->where('created_at','>=',now()->subMonth())
            ->first();

        // --- PSI cache stats (analysis_cache table) ---
        $psiStats = DB::table('analysis_cache')
            ->select(
                DB::raw('COUNT(*) as entries'),
                DB::raw('MAX(updated_at) as last_update')
            )
            ->first();

        return view('admin.dashboard', compact(
            'metrics','dailyUsage','topUsers','openaiUsage','psiStats'
        ));
    }
}
