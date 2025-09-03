<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TouchPresence
{
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check()) {
            $user = Auth::user();
            $user->last_seen_at = now();
            $user->last_ip = $request->ip();
            // In a real app, get country from IP
            $user->last_country = 'United States'; 
            $user->save();
        }

        return $next($request);
    }
}
