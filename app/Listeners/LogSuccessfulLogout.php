<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Logout;
use Illuminate\Support\Facades\DB;

class LogSuccessfulLogout
{
    public function handle(Logout $event): void
    {
        try {
            $user = $event->user;
            DB::table('user_sessions')->where('user_id', $user->id)->orderByDesc('id')->limit(1)->update([
                'logout_at' => now(),
                'logout_ip' => request()->ip(),
                'updated_at'=> now(),
            ]);
            DB::table('users')->where('id', $user->id)->update(['last_logout_at' => now()]);
        } catch (\Throwable $e) {}
    }
}
