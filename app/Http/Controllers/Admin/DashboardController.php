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
        // Stats for Header Cards
        $totalUsers = User::count();
        $searchesToday = AnalyzeLog::whereDate('created_at', today())->count();
        $activeUsers = AnalyzeLog::where('created_at', '>=', now()->subMinutes(5))
            ->distinct('user_id')
            ->count();

        // Data for OpenAI & PSI Panels
        $openAiCostToday = OpenAiUsage::whereDate('created_at', today())->sum('cost');
        
        // Fetching the user list for the main table
        $users = User::with('limit')->latest()->paginate(10);

        return view('admin.dashboard', compact(
            'totalUsers',
            'searchesToday',
            'activeUsers',
            'openAiCostToday',
            'users'
        ));
    }
}
