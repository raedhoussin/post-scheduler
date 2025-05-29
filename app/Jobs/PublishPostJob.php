<?php

namespace App\Jobs;

use App\Models\Post;
use App\Models\Platform;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

use Throwable;

/**
 * This job handles the publishing of a post to a specific platform.
 * It implements ShouldQueue to run asynchronously in the queue system.
 */
class PublishPostJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    // Job will attempt 3 times before failing completely
    public $tries = 3;
    
    // Delay between retries (in seconds)
    public $backoff = [30, 60, 120];

    /**
     * Create a new job instance.
     *
     * @param int $postId The ID of the post to publish
     * @param int $platformId The ID of the platform to publish to
     */
    public function __construct(
        public int $postId,
        public int $platformId
    ) {}

    /**
     * Execute the job.
     * This is the main entry point for the job processing.
     */
    public function handle(): void
    {
        try {
            // Eager load the post with its platforms relationship, filtered for our specific platform
            $post = Post::with(['platforms' => function ($query) {
                $query->where('platforms.id', $this->platformId);
            }])->find($this->postId);

            // Get the platform we're publishing to
            $platform = Platform::find($this->platformId);

            // Validate that both resources exist and are properly associated
            $this->validateResources($post, $platform);
            
            // Process the actual publishing
            $this->processPublishing($post, $platform);
            
        } catch (Throwable $e) {
            // Handle any exceptions that occur during processing
            $this->handleFailure($e);
            throw $e; // Re-throw to allow Laravel to handle job failure
        }
    }

    /**
     * Handle job failure by updating post status and logging the error.
     *
     * @param Throwable $exception The exception that caused the failure
     */
    protected function handleFailure(Throwable $exception): void
    {
        // Reload the post and platform to ensure we have fresh data
        $post = Post::find($this->postId);
        $platform = Platform::find($this->platformId);

        if ($post && $platform) {
            $this->markAsFailed(
                $post, 
                $platform, 
                "Job failed: " . $exception->getMessage()
            );
        }

        // Log detailed error information
        Log::error("PublishPostJob failure", [
            'post_id' => $this->postId,
            'platform_id' => $this->platformId,
            'error' => $exception->getMessage(),
            'attempts' => $this->attempts(),
            'trace' => $exception->getTraceAsString()
        ]);
    }

    /**
     * Validate that required resources exist and are properly associated.
     *
     * @param Post|null $post The post to validate
     * @param Platform|null $platform The platform to validate
     * @throws \RuntimeException When validation fails
     */
    protected function validateResources(?Post $post, ?Platform $platform): void
    {
        if (!$post) {
            throw new \RuntimeException("Post not found with ID: {$this->postId}");
        }

        if (!$platform) {
            throw new \RuntimeException("Platform not found with ID: {$this->platformId}");
        }

        // Check if the post is actually associated with the platform
        if (!$post->platforms->contains($platform->id)) {
            throw new \RuntimeException("Post {$post->id} is not associated with Platform {$platform->id}");
        }
    }

    /**
     * Process the publishing workflow.
     *
     * @param Post $post The post to publish
     * @param Platform $platform The platform to publish to
     */
    protected function processPublishing(Post $post, Platform $platform): void
    {
        // Check platform-specific constraints (like character limits)
        if (!$this->checkPlatformConstraints($post->content, $platform)) {
            $this->markAsFailed($post, $platform, 'Platform constraints check failed');
            return;
        }

        Log::info("Starting publishing process for post {$post->id} to {$platform->name}");

        // Attempt to publish to the platform
        $publishSuccess = $this->publishToPlatform($post, $platform);

        // Update status based on result
        if ($publishSuccess) {
            $this->markAsPublished($post, $platform);
        } else {
            $this->markAsFailed($post, $platform, 'Publishing process returned false');
        }
    }

    /**
     * Publish the post to the specified platform.
     *
     * @param Post $post The post to publish
     * @param Platform $platform The platform to publish to
     * @return bool True if publishing succeeded, false otherwise
     */
    protected function publishToPlatform(Post $post, Platform $platform): bool
    {
        try {
            // Route to the appropriate platform-specific publishing method
            if ($platform->type === 'twitter') {
                $this->publishToTwitter($post);
            } elseif ($platform->type === 'linkedin') {
                $this->publishToLinkedIn($post);
            }
            
            return true;
            
        } catch (Throwable $e) {
            Log::error("Platform publishing failed", [
                'platform' => $platform->type,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Simulate publishing to Twitter (placeholder implementation).
     *
     * @param Post $post The post to publish
     */
    protected function publishToTwitter(Post $post): void
    {
        sleep(1); // Simulate API call delay
    }

    /**
     * Simulate publishing to LinkedIn (placeholder implementation).
     *
     * @param Post $post The post to publish
     */
    protected function publishToLinkedIn(Post $post): void
    {
        sleep(1); // Simulate API call delay
    }

    /**
     * Check if the post content meets platform-specific constraints.
     *
     * @param string $content The post content to check
     * @param Platform $platform The platform to check against
     * @return bool True if constraints are satisfied, false otherwise
     */
    protected function checkPlatformConstraints(string $content, Platform $platform): bool
    {
        // Define constraints for each platform type
        $constraints = [
            'twitter' => mb_strlen($content) <= 280,    // Twitter character limit
            'linkedin' => mb_strlen($content) <= 1300,  // LinkedIn character limit
            'instagram' => true,                       // No content length check for Instagram
        ];

        // Return the constraint check for this platform, defaulting to true if not specified
        return $constraints[$platform->type] ?? true;
    }

    /**
     * Update the post-platform pivot to mark as published.
     *
     * @param Post $post The published post
     * @param Platform $platform The platform it was published to
     */
    protected function markAsPublished(Post $post, Platform $platform): void
    {
      /*  $post->platforms()->updateExistingPivot($platform->id, [
            'status' => 'published',
        ]);
        */
        DB::table('post_platforms') 
            ->where('post_id', $post->id)
            ->where('platform_id', $platform->id)
            ->update([
                'status' => 'published',
            ]);

        Log::info("Post {$post->id} successfully published to {$platform->name}");
    }

    /**
     * Update the post-platform pivot to mark as failed.
     *
     * @param Post $post The post that failed to publish
     * @param Platform $platform The platform it failed to publish to
     * @param string $reason The reason for the failure
     */
    protected function markAsFailed(Post $post, Platform $platform, string $reason): void
    {
        DB::table('post_platforms') 
        ->where('post_id', $post->id)
        ->where('platform_id', $platform->id)
        ->update([
            'status' => 'failed',
        ]);

        Log::warning("Post {$post->id} failed to publish to {$platform->name}: {$reason}");
    }

    /**
     * Called when the job has failed all its attempts.
     *
     * @param Throwable $exception The exception that caused the failure
     */
    public function failed(Throwable $exception): void
    {
        $this->handleFailure($exception);
        
        // You could add notification logic here
        $this->notifyAdmins($exception);
    }

    /**
     * Placeholder for admin notification logic.
     *
     * @param Throwable $exception The exception to notify about
     */
    protected function notifyAdmins(Throwable $exception): void
    {
        // TODO: Implement admin notification logic
        // Could use Mail, Notification, Slack, etc.
    }
}