<?php
namespace Database\Seeders;

use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Database\Seeder;

class ActivityLogSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::all();

        if ($users->isEmpty()) {
            $this->command->info('⚠️ No users found in the database. Please seed users first.');
            return;
        }

        foreach ($users as $user) {
            ActivityLog::factory()->count(10)->create([
                'user_id' => $user->id,
            ]);
        }

        $this->command->info('✅ Activity logs generated for existing users.');
    }
}