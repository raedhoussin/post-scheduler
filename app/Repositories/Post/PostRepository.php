<?php

namespace App\Repositories\Post;

use App\Models\Post;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Carbon;
use App\Services\Post\ScheduledPostLimitValidator;


class PostRepository implements PostRepositoryInterface
{
    public function __construct(
        protected ScheduledPostLimitValidator $limitValidator
    ) {}
    
    public function paginateByUser(int $userId, array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = Post::where('user_id', $userId);

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['from_date'])) {
            $query->whereDate('created_at', '>=', $filters['from_date']);
        }

        if (!empty($filters['to_date'])) {
            $query->whereDate('created_at', '<=', $filters['to_date']);
        }

        return $query->with('platforms')->paginate($perPage);
    }

    public function create(array $data): Post
    {
        $this->limitValidator->validate($data);

        return Post::create($data);
    }

    public function findByIdAndUser(int $id, int $userId): ?Post
    {
        return Post::with('platforms')->where('user_id', $userId)->find($id);
    }

    public function update(Post $post, array $data): Post
    {
        $post->fill($data);
        $post->save();

        return $post;
    }

    public function delete(Post $post): void
    {
        $post->platforms()->detach();
        $post->delete();
    }

    public function publish(Post $post): Post
    {
        $post->status = 'published';
        $post->scheduled_at = Carbon::now();
        $post->save();

        return $post;
    }
}