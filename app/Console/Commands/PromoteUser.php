<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;

class PromoteUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * Usage: php artisan user:promote {email} {role=admin}
     */
    protected $signature = 'user:promote {email} {role=admin}';

    /**
     * The console command description.
     */
    protected $description = 'Promote a user to a given role (default: admin)';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $email = $this->argument('email');
        $role  = $this->argument('role');

        $user = User::where('email', $email)->first();

        if (!$user) {
            $this->error("❌ User with email {$email} not found.");
            return 1;
        }

        $user->role = $role;
        $user->save();

        $this->info("✅ User {$user->email} promoted to role: {$role}");
        return 0;
    }
}
