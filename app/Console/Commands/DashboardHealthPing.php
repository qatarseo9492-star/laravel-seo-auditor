<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class DashboardHealthPing extends Command
{
    protected $signature = 'dashboard:health-ping';
    protected $description = 'Update the dashboard scheduler heartbeat timestamp (read-only cache).';

    public function handle(): int
    {
        Cache::put('dash:heartbeat_at', now(), 180); // 3 minutes TTL
        $this->info('dashboard:health-ping timestamp updated.');
        return self::SUCCESS;
    }
}
