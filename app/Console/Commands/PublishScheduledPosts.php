<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Post;
use Carbon\Carbon;
use App\Jobs\PublishPostJob;
use Illuminate\Support\Facades\Log;

class PublishScheduledPosts extends Command
{
    protected $signature = 'posts:publish-scheduled';
    protected $description = 'Publish posts that are scheduled for the current time';

    public function handle()
    {
        $now = Carbon::now()->toDateTimeString();

        $this->info("Starting scheduled posts publishing process at {$now}");

        // Get posts scheduled for current time or earlier
        $posts = Post::where('status', 'scheduled')
           // ->where('scheduled_at', '<=', $now)
            ->with('platforms')
            ->get();

        if ($posts->isEmpty()) {
            $this->info('No scheduled posts found for publishing.');
            return 0;
        }

        $this->info("Found {$posts->count()} posts to publish.");

        foreach ($posts as $post) {
            try {
                $this->info("Processing Post ID: {$post->id}");

                if ($post->platforms->isEmpty()) {
                    $this->warn("No platforms associated with Post ID: {$post->id}");
                    continue;
                }

                // Dispatch job for each platform
                foreach ($post->platforms as $platform) {
                    PublishPostJob::dispatch($post->id, $platform->id)
                        ->onQueue('publishing');
                    
                    $this->info("Dispatched job for Post ID {$post->id} on Platform ID {$platform->id}");
                }

                // Update post status only after dispatching all jobs
             //   $post->update(['status' => 'published']);
                $this->info("Updated Post ID {$post->id} status to published");

            } catch (\Exception $e) {
                Log::error("Failed to process Post ID {$post->id}: " . $e->getMessage());
                $this->error("Error processing Post ID {$post->id}: " . $e->getMessage());
            }
        }

        $this->info('Scheduled posts publishing completed successfully.');
        return 0;
    }
}