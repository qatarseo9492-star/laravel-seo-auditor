<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Login;
use Illuminate\Support\Facades\DB;

class LogSuccessfulLogin
{
    public function handle(Login $event): void
    {
        try {
            $user = $event->user;
            DB::table('user_sessions')->insert([
                'user_id'    => $user->id,
                'login_at'   => now(),
                'login_ip'   => request()->ip(),
                'user_agent' => substr((string)request()->userAgent(), 0, 250),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            // Update last_login_at on users table (additive column)
            DB::table('users')->where('id', $user->id)->update(['last_login_at' => now()]);
        } catch (\Throwable $e) {}
    }
}
