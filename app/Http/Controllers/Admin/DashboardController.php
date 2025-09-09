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
    public function index(Request $request)
    {
        // Define all the data variables the view will need
        $stats = $this->getKpiStats();
        $systemHealth = $this->getSystemHealth();
        $limitsSummary = $this->getUserLimitsSummary();
        $trafficData = $this->getTrafficData();
        $history = $this->getGlobalHistory();

        // The user data from your original controller was good, let's keep it.
        // It's often better to show users on a dedicated user management page.
        $users = User::with('limit')->latest()->paginate(10);
        $users->getCollection()->transform(function ($u) {
            $u->today_count = AnalyzeLog::where('user_id', $u->id)
                              ->whereDate('created_at', today())->count();
            return $u;
        });


        // Pass all the variables to the view using compact()
        return view('admin.dashboard', compact(
            'stats',
            'systemHealth',
            'limitsSummary',
            'trafficData',
            'history',
            'users'
        ));
    }

    // --- Helper methods to fetch data ---

    private function getKpiStats() {
        $stats = [
            'searchesToday' => AnalyzeLog::whereDate('created_at', today())->count(),
            'totalUsers' => User::count(),
            'cost24h' => (float) OpenAiUsage::where('created_at', '>=', now()->subDay())->sum('cost'),
            'dau' => AnalyzeLog::where('created_at', '>=', now()->subDay())->distinct('user_id')->count(),
            'mau' => AnalyzeLog::where('created_at', '>=', now()->subDays(30))->distinct('user_id')->count(),
            'active5m' => User::where('updated_at', '>=', now()->subMinutes(5))->count(),
            'tokens24h' => 0 // Default to 0
        ];

        // **FIX**: Check if the 'tokens' column exists before trying to sum it.
        if (Schema::hasColumn('open_ai_usages', 'tokens')) {
            $stats['tokens24h'] = OpenAiUsage::where('created_at', '>=', now()->subDay())->sum('tokens');
        }

        return $stats;
    }

    private function getSystemHealth() {
        // **FIX**: Changed 'latency_ms' to 'latency' to match the view's expectation.
        return [
            ['name' => 'Main API', 'status' => 'Operational', 'latency' => 5, 'ok' => true],
            ['name' => 'Database Cluster', 'status' => 'Operational', 'latency' => 2, 'ok' => true],
        ];
    }
    
    private function getUserLimitsSummary() {
        return [
            'enabled' => User::whereHas('limit', fn($q) => $q->where('is_enabled', true))->count(),
            'disabled' => User::whereHas('limit', fn($q) => $q->where('is_enabled', false))->count(),
            'default' => 200,
        ];
    }
    
    private function getTrafficData() {
        $traffic = AnalyzeLog::select(DB::raw('DATE(created_at) as date'), DB::raw('count(*) as count'))
            ->where('created_at', '>=', now()->subDays(30))
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get();
        
        return [
            'labels' => $traffic->pluck('date'),
            'data' => $traffic->pluck('count'),
        ];
    }

    private function getGlobalHistory() {
        $logTable = 'analyze_logs';
        
        // **FIX**: Build the select query dynamically based on existing columns.
        $selectColumns = ['id', 'user_id', 'created_at', 'tool'];
        if (Schema::hasColumn($logTable, 'cost')) {
            $selectColumns[] = 'cost';
        }
        if (Schema::hasColumn($logTable, 'tokens')) {
            $selectColumns[] = 'tokens';
        }
        $displayColumn = collect(['query', 'keyword', 'url'])->first(fn($c) => Schema::hasColumn($logTable, $c));
        if ($displayColumn) {
            $selectColumns[] = $displayColumn;
        }

        return AnalyzeLog::with('user:id,email')
            ->select($selectColumns)
            ->latest()
            ->limit(100)
            ->get()
            ->map(function ($log) use ($displayColumn) {
                // The view will safely handle missing attributes with '??'
                if ($displayColumn) {
                    $log->display = $log->{$displayColumn};
                }
                return $log;
            });
    }
}
