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
// Optional: MRR if you have a subscriptions table
$mrr = null;
if (Schema::hasTable('subscriptions')) {
$mrr = '$' . number_format((float) DB::table('subscriptions')
->where('status', 'active')
->sum('mrr_cents') / 100, 2);
}


return view('admin.dashboard', [
'kpis' => $kpis,
'mrr' => $mrr,
]);
}


public function live(Request $request)
{
// KPIs + health + history + traffic — lightweight and resilient
return response()->json([
'kpis' => $this->computeKpis(),
'services' => $this->serviceHealth(),
'history' => $this->recentHistory(),
'traffic' => $this->trafficSeries(),
]);
}


private function computeKpis(): array
{
$today = now()->startOfDay();
$k = [
'searchesToday' => 0,
'totalUsers' => 0,
'cost24h' => 0.0,
'dau' => 0,
'mau' => 0,
'active5m' => 0,
'dailyLimit' => 100,
];


// Users
if (Schema::hasTable('users')) {
$k['totalUsers'] = (int) User::count();
// Active in last 5 minutes (if you track last_seen_at presence)
if (Schema::hasColumn('users', 'last_seen_at')) {
$k['active5m'] = (int) User::where('last_seen_at', '>=', now()->subMinutes(5))->count();
}
}


// Analyses
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


// Cost in last 24h
if (Schema::hasTable('openai_usage')) {
$k['cost24h'] = (float) DB::table('openai_usage')
->where('created_at', '>=', now()->subDay())
->sum('cost_usd');
}
