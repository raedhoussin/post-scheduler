<?php 

namespace App\Services\Post;

use App\Models\Post;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class ScheduledPostLimitValidator
{
    public function validate(array $data): void
    {
        if (!isset($data['status']) || $data['status'] !== 'scheduled') {
            return;
        }

        if (!isset($data['scheduled_at'])) {
            throw new \InvalidArgumentException("Scheduled date is required for scheduled posts.");
        }

        $userId = $data['user_id'] ?? Auth::id();
        $scheduledDate = Carbon::parse($data['scheduled_at'])->toDateString();

        $count = Post::where('user_id', $userId)
            ->whereDate('scheduled_at', $scheduledDate)
            ->where('status', 'scheduled')
            ->count();

        $limit = config('posts.daily_schedule_limit');

        if ($count >= $limit) {
            throw new \Exception("You have reached the daily limit of {$limit} scheduled posts.");
        }
    }
}