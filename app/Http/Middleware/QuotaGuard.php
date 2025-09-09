<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class QuotaGuard
{
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }

        // Map path to canonical tool names for per-tool tracking (optional)
        $path = '/'.ltrim($request->path(), '/');
        $tool = match (true) {
            str_ends_with($path, '/api/semantic-analyze')         => 'semantic',
            str_ends_with($path, '/api/technical-seo-analyze')    => 'technical',
            str_ends_with($path, '/api/keyword-analyze')          => 'keyword',
            str_ends_with($path, '/api/content-engine-analyze')   => 'content_engine',
            str_ends_with($path, '/semantic-analyzer/psi')        => 'psi',
            default                                               => 'analyzer',
        };

        // Read limits — prefer users table columns if they exist; otherwise fall back to user_limits
        $dailyLimit   = $user->daily_quota   ?? null;
        $monthlyLimit = $user->monthly_quota ?? null;

        // Fallback to user_limits table if user fields are null
        if (is_null($dailyLimit) || is_null($monthlyLimit)) {
            try {
                if (DB::getSchemaBuilder()->hasTable('user_limits')) {
                    $ul = DB::table('user_limits')->where('user_id', $user->id)->first();
                    if ($ul) {
                        $dailyLimit   = $dailyLimit   ?? ($ul->daily_limit   ?? null);
                        $monthlyLimit = $monthlyLimit ?? ($ul->monthly_limit ?? null);
                    }
                }
            } catch (\Throwable $e) {
                // ignore if table missing; we’ll just not enforce limits
            }
        }

        // ------------------------
        // ✅ Add +100 to everyone's daily limit (bonus)
        //    If you want to enforce exactly 100 as default instead, replace the next line with:
        //    $dailyLimit = $dailyLimit ?? 100;
        $dailyLimit = is_null($dailyLimit) ? 100 : ($dailyLimit + 100);
        // ------------------------

        // If no limits configured anywhere (monthly only), we still enforce daily via the line above.
        // If you also want a monthly bonus, you can do the same pattern here:
        // $monthlyLimit = is_null($monthlyLimit) ? null : ($monthlyLimit + 100);

        $startOfDay   = now()->startOfDay();
        $startOfMonth = now()->startOfMonth();

        // Count usage (global, not per-tool). If you want per-tool, add ->where('tool', $tool)
        $dailyCount = DB::table('analyze_logs')
            ->where('user_id', $user->id)
            ->where('created_at', '>=', $startOfDay)
            ->count();

        $monthlyCount = DB::table('analyze_logs')
            ->where('user_id', $user->id)
            ->where('created_at', '>=', $startOfMonth)
            ->count();

        if (!is_null($dailyLimit) && $dailyCount >= $dailyLimit) {
            return response()->json(['message' => 'Daily quota reached.'], 429);
        }
        if (!is_null($monthlyLimit) && $monthlyCount >= $monthlyLimit) {
            return response()->json(['message' => 'Monthly quota reached.'], 429);
        }

        return $next($request);
    }
}
