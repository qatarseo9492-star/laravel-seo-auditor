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
    /**
     * Display the admin dashboard.
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        // Define all the data variables the view will need
        $stats = $this->getKpiStats();
        $systemHealth = $this->getSystemHealth(); // This defines the missing variable
        $limitsSummary = $this->getUserLimitsSummary();
        $trafficData = $this->getTrafficData();
        $history = $this->getGlobalHistory();

        // Pass all the variables to the view using compact()
        return view('admin.dashboard', compact(
            'stats',
            'systemHealth',
            'limitsSummary',
            'trafficData',
            'history'
        ));
    }

    // --- Helper methods to fetch data ---

    private function getKpiStats() {
        // Replace with your actual queries. Using mock data for now.
        return [
            'searchesToday' => AnalyzeLog::whereDate('created_at', today())->count(),
            'totalUsers' => User::count(),
            'cost24h' => (float) OpenAiUsage::where('created_at', '>=', now()->subDay())->sum('cost'),
            'tokens24h' => OpenAiUsage::where('created_at', '>=', now()->subDay())->sum('tokens'),
            'dau' => AnalyzeLog::where('created_at', '>=', now()->subDay())->distinct('user_id')->count(),
            'mau' => AnalyzeLog::where('created_at', '>=', now()->subDays(30))->distinct('user_id')->count(),
            'active5m' => User::where('updated_at', '>=', now()->subMinutes(5))->count(),
        ];
    }

    private function getSystemHealth() {
        // Replace with your actual service checks. Using mock data for demonstration.
        return [
            ['name' => 'Main API', 'status' => 'Operational', 'latency_ms' => 5, 'ok' => true],
            ['name' => 'Database Cluster', 'status' => 'Operational', 'latency_ms' => 2, 'ok' => true],
            ['name' => 'Cache Service', 'status' => 'Degraded', 'latency_ms' => 89, 'ok' => false],
            ['name' => 'Billing Endpoint', 'status' => 'Operational', 'latency_ms' => 25, 'ok' => true],
        ];
    }
    
    private function getUserLimitsSummary() {
        // Replace with your actual queries.
        return [
            'enabled' => User::whereHas('limit', fn($q) => $q->where('is_enabled', true))->count(),
            'disabled' => User::whereHas('limit', fn($q) => $q->where('is_enabled', false))->count(),
            'default' => 200, // Your default limit
        ];
    }
    
    private function getTrafficData() {
        // Replace with your actual traffic data query.
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
        // Add a 'display' column for easier access in the view
        return AnalyzeLog::with('user:id,email')
            ->latest()
            ->limit(100)
            ->get()
            ->map(function ($log) {
                // Find the first available field to display as the query
                $log->display = $log->query ?? $log->url ?? $log->keyword ?? 'N/A';
                return $log;
            });
    }
}

