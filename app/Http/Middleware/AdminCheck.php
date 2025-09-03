<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminCheck
{
    /**
     * Handle an incoming request.
     *
     * This middleware checks if the authenticated user has the 'admin' role.
     * If not, it redirects them to the regular user dashboard.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // This is the crucial check.
        // It verifies the user is logged in AND their 'role' column is 'admin'.
        if (!Auth::check() || Auth::user()->role !== 'admin') {
            // If the check fails, redirect them away from the admin area.
            return redirect('/dashboard')->with('error', 'You do not have permission to access the admin panel.');
        }

        return $next($request);
    }
}
