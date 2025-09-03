<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BanCheck
{
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check() && Auth::user()->is_banned) {
            Auth::logout();
            return redirect('/login')->with('error', 'Your account has been suspended.');
        }

        return $next($request);
    }
}
