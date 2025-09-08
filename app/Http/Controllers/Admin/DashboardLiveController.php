<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

use App\Models\User;
use App\Models\AnalyzeLog;
use App\Models\OpenAiUsage;

class DashboardLiveController extends Controller
{
    public function live(Request $request)
    {
        // KPIs (10s cache)
        $kpis = Cache::remember('dashlive:kpis', 10, function () {
            $out = [
                'totalUsers'     => 0,
                'searchesToday'  => 0,
                'active5m'       => 0,
                'active24h'      => 0,
                'tokens24h'      => 0,
                'cost24h'        => 0.0,
                'dau'            => 0,
                'mau'            => 0,
            ];

            try { $out['totalUsers'] = User::count(); } catch (\Throwable $e) {}
            try { $out['active5m']   = User::where('last_seen_at','>=', now()->subMinutes(5))->count(); } catch (\Throwable $e) {}
            try { $out['active24h']  = User::where('last_seen_at','>=', now()->subDay())->count(); } catch (\Throwable $e) {}

            if (Schema::hasTable((new AnalyzeLog)->getTable())) {
                try { $out['searchesToday'] = AnalyzeLog::whereDate('created_at', now()->toDateString())->count(); } catch (\Throwable $e) {}
                // DAU/MAU (unique users)
                try {
                    $out['dau'] = AnalyzeLog::where('created_at','>=', now()->subDay())->distinct('user_id')->count('user_id');
                } catch (\Throwable $e) {}
                try {
                    $out['mau'] = AnalyzeLog::where('created_at','>=', now()->subDays(30))->distinct('user_id')->count('user_id');
                } catch (\Throwable $e) {}
            }

            if (Schema::hasTable((new OpenAiUsage)->getTable())) {
                try {
                    $out['tokens24h'] = (int) OpenAiUsage::where('created_at','>=', now()->subDay())->sum('tokens');
                    $out['cost24h']   = (float) OpenAiUsage::where('created_at','>=', now()->subDay())->sum('cost_usd');
                } catch (\Throwable $e) {}
            }

            return $out;
        });

        // Services (20s cache)
        $services = Cache::remember('dashlive:services', 20, function () {
            $arr = [];
            try { $t = microtime(true); DB::select('select 1'); $arr[] = ['name'=>'DB','ok'=>true,'latency_ms'=>round((microtime(true)-$t)*1000)]; }
            catch (\Throwable $e) { $arr[] = ['name'=>'DB','ok'=>false,'latency_ms'=>null]; }

            // Queue backlog via jobs table (if present)
            if (Schema::hasTable('jobs')) {
                try { $backlog = DB::table('jobs')->count(); $arr[] = ['name'=>'Queue','ok'=>$backlog<1000,'latency_ms'=>$backlog]; }
                catch (\Throwable $e) { $arr[] = ['name'=>'Queue','ok'=>true,'latency_ms'=>null]; }
            }

            // Scheduler heartbeat
            $beat = Cache::get('dash:heartbeat_at');
            $fresh = false; if ($beat) { try { $fresh = now()->diffInSeconds($beat) <= 120; } catch (\Throwable $e) { $fresh = false; } }
            $arr[] = ['name'=>'Scheduler','ok'=>$fresh,'latency_ms'=>null];

            return $arr;
        });

        // Traffic 30d (60s cache)
        $traffic = Cache::remember('dashlive:traffic', 60, function () {
            if (!Schema::hasTable((new AnalyzeLog)->getTable())) return [];
            $rows = AnalyzeLog::selectRaw('DATE(created_at) as day, COUNT(*) as count')
                ->where('created_at','>=', now()->subDays(29)->startOfDay())
                ->groupBy('day')->orderBy('day')->get();
            return $rows->map(fn($r)=>['day'=>(string)$r->day,'count'=>(int)$r->count])->toArray();
        });

        // Recent history (last 100, 60s cache)
        $history = Cache::remember('dashlive:history', 60, function () {
            if (!Schema::hasTable((new AnalyzeLog)->getTable())) return [];
            $cands = ['query','url','domain','keyword','title'];
            $rows = AnalyzeLog::with('user:id,name,email')->latest()->limit(100)->get();
            return $rows->map(function($h) use ($cands){
                $display = '';
                foreach ($cands as $c) { if (isset($h->$c) && !empty($h->$c)) { $display = (string)$h->$c; break; } }
                return [
                    'when'    => (string) $h->created_at,
                    'user'    => $h->user ? ($h->user->email ?? $h->user->name ?? ('#'.$h->user_id)) : ('#'.$h->user_id),
                    'tool'    => (string) ($h->type ?? '—'),
                    'display' => $display,
                    'tokens'  => (int) ($h->tokens ?? 0),
                    'cost'    => (string) number_format((float)($h->cost_usd ?? 0), 4),
                ];
            })->toArray();
        });

        // Online users list (5m window, 30s cache)
        $online = Cache::remember('dashlive:online', 30, function () {
            try {
                $users = User::where('last_seen_at','>=', now()->subMinutes(5))
                    ->select('id','name','email','last_seen_at','last_ip','last_country','is_banned','is_admin')
                    ->orderByDesc('last_seen_at')->limit(50)->get();
                return $users->toArray();
            } catch (\Throwable $e) { return []; }
        });

        return response()->json([
            'kpis'     => $kpis,
            'services' => $services,
            'traffic'  => $traffic,
            'history'  => $history,
            'online'   => $online,
        ]);
    }

    // Single user quick JSON for inline drawer
    public function user(User $user)
    {
        $limit = $user->limit;
        $sessions = DB::table('user_sessions')->where('user_id',$user->id)->orderByDesc('login_at')->limit(10)->get();
        $latest = AnalyzeLog::where('user_id', $user->id)->latest()->limit(10)->get(['created_at','type','query','url','domain','tokens','cost_usd']);
        return response()->json([
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'is_admin' => (bool) $user->is_admin,
                'is_banned'=> (bool) $user->is_banned,
                'last_seen_at' => optional($user->last_seen_at)->toDateTimeString(),
                'last_ip' => $user->last_ip,
                'last_country' => $user->last_country,
                'last_login_at' => method_exists($user,'getAttribute') ? (string)($user->getAttribute('last_login_at') ?? '') : '',
                'last_logout_at'=> method_exists($user,'getAttribute') ? (string)($user->getAttribute('last_logout_at') ?? '') : '',
            ],
            'limit' => $limit ? [
                'daily_limit' => (int)$limit->daily_limit,
                'is_enabled'  => (bool)$limit->is_enabled,
                'reason'      => (string)$limit->reason,
            ] : null,
            'sessions' => $sessions,
            'latest'   => $latest,
        ]);
    }
}
