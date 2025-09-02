<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        // Replace with your real email
        $email = 'your@email.com';

        $user = User::where('email', $email)->first();

        if ($user) {
            $user->update(['role' => 'admin']);
            $this->command->info("✅ User {$email} promoted to admin.");
        } else {
            $this->command->error("❌ User with {$email} not found.");
        }
    }
}
