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
        // ===== Header cards (keep your originals) =====
        $totalUsers     = User::count();
        $searchesToday  = AnalyzeLog::whereDate('created_at', today())->count();
        $activeUsers    = AnalyzeLog::where('created_at', '>=', now()->subMinutes(5))
                            ->distinct('user_id')->count('user_id');
        $openAiCostToday= (float) OpenAiUsage::whereDate('created_at', today())->sum('cost');

        // ===== Extra stats for the new Blade =====
        $avg7d     = round(
            AnalyzeLog::where('created_at', '>=', now()->subDays(7))->count() / 7, 1
        );
        $costMonth = (float) OpenAiUsage::whereBetween('created_at', [now()->startOfMonth(), now()])->sum('cost');

        $stats = [
            'searchesToday' => $searchesToday,
            'avg7d'         => $avg7d,
            'totalUsers'    => $totalUsers,
            'newUsers'      => User::whereDate('created_at', today())->count(),
            'costToday'     => $openAiCostToday,
            'costMonth'     => $costMonth,
            'active5m'      => $activeUsers,
            // quick peak approximation (count of busiest hour today)
            'peakToday'     => (int) AnalyzeLog::whereDate('created_at', today())
                                 ->selectRaw('HOUR(created_at) h, COUNT(*) c')
                                 ->groupBy('h')->orderByDesc('c')->value('c') ?? 0,
        ];

        // ===== System flags shown in "System Status" =====
        $system = [
            'psi'    => (bool) (env('PAGESPEED_API_KEY') ?: config('services.psi.key')),
            'openai' => (bool) env('OPENAI_API_KEY'),
            'cache'  => app('cache')->getDefaultDriver() ? true : false,
        ];

        // ===== Users (your existing pagination) + per-user usage counts =====
        $users = User::with('limit')->latest()->paginate(10);
        $users->getCollection()->transform(function ($u) {
            $u->today_count = AnalyzeLog::where('user_id', $u->id)
                              ->whereDate('created_at', today())->count();
            $u->month_count = AnalyzeLog::where('user_id', $u->id)
                              ->where('created_at', '>=', now()->startOfMonth())->count();
            // Fallback if you don't have a 'banned' column
            $u->status = property_exists($u, 'banned') ? ($u->banned ? 'Banned' : 'Active') : 'Active';
            return $u;
        });

        // ===== History table (latest 80) =====
        $history = AnalyzeLog::with('user')->latest()->limit(80)->get();

        // ===== Top queries/pages (for the small analytics grid) =====
        $topItems = DB::table((new AnalyzeLog)->getTable())
            ->selectRaw("COALESCE(NULLIF(query, ''), url) AS name, COUNT(*) AS count")
            ->groupBy('name')->orderByDesc('count')->limit(6)->get()
            ->map(fn($r) => ['name' => $r->name, 'count' => (int)$r->count])->toArray();

        return view('admin.dashboard', compact(
            // your original vars (for backward compatibility)
            'totalUsers', 'searchesToday', 'activeUsers', 'openAiCostToday', 'users',
            // new view requirements
            'stats', 'system', 'history', 'topItems'
        ));
    }
}
