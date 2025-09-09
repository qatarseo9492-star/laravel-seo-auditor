<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class DashboardHealthPing extends Command
{
    protected $signature = 'dashboard:health-ping';
    protected $description = 'Record a heartbeat timestamp for the admin dashboard health check';

    public function handle(): int
    {
        Cache::put('dash:heartbeat_at', now(), 180); // 3 min TTL
        $this->info('Dashboard heartbeat recorded at ' . now()->toDateTimeString());
        return self::SUCCESS;
    }
}
