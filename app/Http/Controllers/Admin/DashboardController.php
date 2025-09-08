<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

use App\Models\User;
use App\Models\UserLimit;
use App\Models\AnalyzeLog;
use App\Models\OpenAiUsage;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // Keep your existing behavior; safe defaults
        $services = Cache::remember('dash:services', 60, function () {
            $services = [];
            try { $t = microtime(true); DB::select('select 1'); $services[]=['name'=>'DB','ok'=>true,'latency_ms'=>round((microtime(true)-$t)*1000)]; }
            catch (\Throwable $e) { $services[]=['name'=>'DB','ok'=>false,'latency_ms'=>null]; }
            if (Schema::hasTable('jobs')) {
                try { $backlog = DB::table('jobs')->count(); $services[]=['name'=>'Queue','ok'=>$backlog<1000,'latency_ms'=>$backlog]; }
                catch (\Throwable $e) { $services[]=['name'=>'Queue','ok'=>true,'latency_ms'=>null]; }
            }
            $apiOk = true;
            try {
                if (Schema::hasTable((new OpenAiUsage)->getTable())) {
                    $apiOk = OpenAiUsage::where('created_at','>=', now()->subHours(6))->exists();
                }
            } catch (\Throwable $e) { $apiOk = true; }
            $services[] = ['name'=>'OpenAI API','ok'=>(bool)$apiOk,'latency_ms'=>null];

            $beat = Cache::get('dash:heartbeat_at');
            $fresh = false;
            if ($beat) { try { $fresh = now()->diffInSeconds($beat) <= 120; } catch (\Throwable $e) { $fresh=false; } }
            $services[] = ['name'=>'Scheduler','ok'=>$fresh,'latency_ms'=>null];
            return $services;
        });

        $limitsSummary = Cache::remember('dash:limitsSummary', 60, function () {
            $s = ['enabled'=>0,'disabled'=>0,'default'=>200];
            if (Schema::hasTable('user_limits')) {
                try {
                    $s['enabled']  = (int) DB::table('user_limits')->where('is_enabled',1)->count();
                    $s['disabled'] = (int) DB::table('user_limits')->where('is_enabled',0)->count();
                } catch (\Throwable $e) {}
            }
            return $s;
        });

        $traffic = Cache::remember('dash:traffic', 60, function () {
            try {
                if (!Schema::hasTable((new AnalyzeLog)->getTable())) return [];
                return AnalyzeLog::selectRaw('DATE(created_at) as day, COUNT(*) as count')
                    ->where('created_at','>=', now()->subDays(29)->startOfDay())
                    ->groupBy('day')->orderBy('day')->get()
                    ->map(fn($r)=>['day'=>(string)$r->day,'count'=>(int)$r->count])->toArray();
            } catch (\Throwable $e) { return []; }
        });

        $topQueries = Cache::remember('dash:topQueries7d', 60, function () {
            try {
                if (!Schema::hasTable((new AnalyzeLog)->getTable())) return [];
                $cols = Schema::getColumnListing((new AnalyzeLog)->getTable());
                $hasQuery = in_array('query',$cols,true);
                $hasUrl   = in_array('url',$cols,true);
                $hasType  = in_array('type',$cols,true);
                $hasScore = in_array('score',$cols,true);
                $expr = $hasQuery ? "NULLIF(query,'')" : ($hasUrl ? "NULLIF(url,'')" : ($hasType ? "NULLIF(type,'')" : "'—'"));
                $select = "COALESCE($expr,'—') as q, COUNT(*) as count";
                if ($hasScore) $select .= ", ROUND(AVG(score)) as avg_score";
                $rows = AnalyzeLog::where('created_at','>=', now()->subDays(7))
                    ->selectRaw($select)->groupBy('q')->orderByDesc('count')->limit(10)->get();
                return $rows->map(function($r) use ($hasScore){
                    return ['query'=>(string)($r->q??'—'),'count'=>(int)($r->count??0),'avg_score'=>$hasScore?(int)($r->avg_score??0):null];
                })->toArray();
            } catch (\Throwable $e) { return []; }
        });

        $errors = Cache::remember('dash:errors24h', 60, function () {
            try {
                if (!Schema::hasTable('failed_jobs')) return [];
                $rows = DB::table('failed_jobs')
                    ->where('failed_at','>=', now()->subDay())
                    ->selectRaw('SUBSTRING(error,1,120) as message, COUNT(*) as count, MAX(failed_at) as last_seen')
                    ->groupBy('message')->orderByDesc('count')->limit(10)->get();
                return collect($rows)->map(fn($e)=>['message'=>(string)($e->message??'—'),'count'=>(int)($e->count??0),'last_seen'=>(string)($e->last_seen??'')])->toArray();
            } catch (\Throwable $e) { return []; }
        });

        // Render your existing view (our Blade uses null-safe defaults)
        return view('admin.dashboard', compact('services','limitsSummary','traffic','topQueries','errors'));
    }

    // === Built-in JSON endpoints used by the inline JS ===

    public function live(Request $request)
    {
        // KPIs
        $kpis = Cache::remember('dashlive:kpis', 10, function () {
            $out = ['totalUsers'=>0,'searchesToday'=>0,'active5m'=>0,'active24h'=>0,'tokens24h'=>0,'cost24h'=>0.0,'dau'=>0,'mau'=>0];
            try { $out['totalUsers'] = User::count(); } catch (\Throwable $e) {}
            try { $out['active5m']   = User::where('last_seen_at','>=', now()->subMinutes(5))->count(); } catch (\Throwable $e) {}
            try { $out['active24h']  = User::where('last_seen_at','>=', now()->subDay())->count(); } catch (\Throwable $e) {}
            if (Schema::hasTable((new AnalyzeLog)->getTable())) {
                try { $out['searchesToday'] = AnalyzeLog::whereDate('created_at', now()->toDateString())->count(); } catch (\Throwable $e) {}
                try { $out['dau'] = AnalyzeLog::where('created_at','>=', now()->subDay())->distinct('user_id')->count('user_id'); } catch (\Throwable $e) {}
                try { $out['mau'] = AnalyzeLog::where('created_at','>=', now()->subDays(30))->distinct('user_id')->count('user_id'); } catch (\Throwable $e) {}
            }
            if (Schema::hasTable((new OpenAiUsage)->getTable())) {
                try { $out['tokens24h'] = (int) OpenAiUsage::where('created_at','>=', now()->subDay())->sum('tokens'); } catch (\Throwable $e) {}
                try { $out['cost24h']   = (float) OpenAiUsage::where('created_at','>=', now()->subDay())->sum('cost_usd'); } catch (\Throwable $e) {}
            }
            return $out;
        });

        // Services
        $services = Cache::remember('dashlive:services', 20, function () {
            $arr = [];
            try { $t=microtime(true); DB::select('select 1'); $arr[]=['name'=>'DB','ok'=>true,'latency_ms'=>round((microtime(true)-$t)*1000)]; }
            catch (\Throwable $e) { $arr[]=['name'=>'DB','ok'=>false,'latency_ms'=>null]; }
            if (Schema::hasTable('jobs')) { try { $backlog=DB::table('jobs')->count(); $arr[]=['name'=>'Queue','ok'=>$backlog<1000,'latency_ms'=>$backlog]; } catch (\Throwable $e) { $arr[]=['name'=>'Queue','ok'=>true,'latency_ms'=>null]; } }
            $beat = Cache::get('dash:heartbeat_at'); $fresh=false; if ($beat) { try { $fresh = now()->diffInSeconds($beat) <= 120; } catch (\Throwable $e) {} }
            $arr[]=['name'=>'Scheduler','ok'=>$fresh,'latency_ms'=>null];
            return $arr;
        });

        // Traffic (30d)
        $traffic = Cache::remember('dashlive:traffic', 60, function () {
            if (!Schema::hasTable((new AnalyzeLog)->getTable())) return [];
            return AnalyzeLog::selectRaw('DATE(created_at) as day, COUNT(*) as count')
                ->where('created_at','>=', now()->subDays(29)->startOfDay())
                ->groupBy('day')->orderBy('day')->get()
                ->map(fn($r)=>['day'=>(string)$r->day,'count'=>(int)$r->count])->toArray();
        });

        // Global history (100 newest)
        $history = Cache::remember('dashlive:history', 10, function () {
            if (!Schema::hasTable((new AnalyzeLog)->getTable())) return [];
            $rows = AnalyzeLog::with('user:id,name,email')->orderByDesc('id')->limit(100)->get(['id','user_id','created_at','type','query','url','domain','tokens','cost_usd']);
            return $rows->map(function($h){
                $display = $h->query ?: ($h->url ?: ($h->domain ?: '—'));
                return [
                    'when' => optional($h->created_at)->toDateTimeString(),
                    'user' => $h->user ? ($h->user->email ?? $h->user->name ?? ('#'.$h->user_id)) : ('#'.$h->user_id),
                    'tool' => (string)($h->type ?? '—'),
                    'display' => (string)$display,
                    'tokens' => (int)($h->tokens ?? 0),
                    'cost' => number_format((float)($h->cost_usd ?? 0), 4),
                ];
            })->toArray();
        });

        return response()->json([
            'kpis' => $kpis,
            'services' => $services,
            'traffic' => $traffic,
            'history' => $history,
        ]);
    }

    public function userLive(User $user)
    {
        $limit = $user->limit;
        $sessions = Schema::hasTable('user_sessions')
            ? DB::table('user_sessions')->where('user_id',$user->id)->orderByDesc('login_at')->limit(10)->get()
            : collect([]);
        $latest = Schema::hasTable((new AnalyzeLog)->getTable())
            ? AnalyzeLog::where('user_id',$user->id)->latest()->limit(10)->get(['created_at','type','query','url','domain','tokens','cost_usd'])
            : collect([]);

        return response()->json([
            'user'=>[
                'id'=>$user->id,'name'=>$user->name,'email'=>$user->email,
                'is_admin'=>(bool)$user->is_admin,'is_banned'=>(bool)$user->is_banned,
                'last_seen_at'=>optional($user->last_seen_at)->toDateTimeString(),
                'last_ip'=>$user->last_ip,'last_country'=>$user->last_country,
                'last_login_at'=>method_exists($user,'getAttribute')?(string)($user->getAttribute('last_login_at')??''):'',
                'last_logout_at'=>method_exists($user,'getAttribute')?(string)($user->getAttribute('last_logout_at')??''):'',
            ],
            'limit'=>$limit?['daily_limit'=>(int)$limit->daily_limit,'is_enabled'=>(bool)$limit->is_enabled,'reason'=>(string)$limit->reason]:null,
            'sessions'=>$sessions,
            'latest'=>$latest,
        ]);
    }
}
