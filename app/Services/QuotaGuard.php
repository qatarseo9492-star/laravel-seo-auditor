<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\UserLimit;

class QuotaGuard
{
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check()) {
            return $next($request);
        }

        $user = Auth::user();
        $limit = UserLimit::firstOrCreate(['user_id' => $user->id]);
        
        // Reset counters if a new day/month has started
        if ($limit->updated_at->isToday() === false) {
            $limit->searches_today = 0;
        }
        if ($limit->updated_at->isSameMonth(now()) === false) {
            $limit->searches_this_month = 0;
        }
        $limit->save();

        if ($limit->searches_today >= $limit->daily_limit || $limit->searches_this_month >= $limit->monthly_limit) {
            return response()->json(['error' => 'You have reached your usage quota.'], 429);
        }

        return $next($request);
    }
}
