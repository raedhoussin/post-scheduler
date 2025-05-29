<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Platform;
use App\Models\Post;
use Illuminate\Support\Str;

class PostSeeder extends Seeder
{
    public function run()
    {
        $users = User::all();
        $platforms = Platform::all();

        if ($users->isEmpty() || $platforms->isEmpty()) {
            $this->command->info('No users or platforms found, skipping posts seeding.');
            return;
        }

        foreach ($users as $user) {
            $postsCount = rand(3, 6); // عدد منشورات لكل مستخدم

            for ($i = 0; $i < $postsCount; $i++) {
                $post = Post::create([
                    'title' => 'Sample Post ' . Str::random(5),
                    'content' => 'This is a scheduled post for user ' . $user->name,
                    'image_url' => 'https://picsum.photos/seed/' . Str::random(10) . '/600/400',
                    'scheduled_at' => now()->addHour(),
                    'status' => 'scheduled',
                    'user_id' => $user->id,
                ]);

                // إرفاق 1 إلى 3 منصات عشوائية
                $platformsToAttach = $platforms->random(rand(1, min(3, $platforms->count())))->pluck('id')->toArray();

                foreach ($platformsToAttach as $platformId) {
                    $post->platforms()->attach($platformId, ['status' => 'pending']);
                }
            }
        }

        $this->command->info('Scheduled posts seeded successfully.');
    }
}