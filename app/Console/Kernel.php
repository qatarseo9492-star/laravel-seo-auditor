<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected function schedule(Schedule $schedule): void
    {
        // Dashboard heartbeat every minute (for System Health card)
        $schedule->command('dashboard:health-ping')->everyMinute();

        // Optional rollups (safe to enable later)
        // $schedule->job(new \App\Jobs\ComputeDailyAnalytics)->dailyAt('00:10');
        // $schedule->job(new \App\Jobs\SnapshotTopQueries(7))->everyFifteenMinutes();
    }

    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');
        require base_path('routes/console.php');
    }
}
