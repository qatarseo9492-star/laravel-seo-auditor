<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // Dashboard heartbeat every minute (for System Health card)
        $schedule->command('dashboard:health-ping')->everyMinute();

        // Optional (safe, read-only rollups) — uncomment when jobs exist:
        // $schedule->job(new \App\Jobs\ComputeDailyAnalytics)->dailyAt('00:10');
        // $schedule->job(new \App\Jobs\SnapshotTopQueries(7))->everyFifteenMinutes();
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        // Loads all commands in app/Console/Commands (including DashboardHealthPing)
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
