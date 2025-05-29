<?php

namespace App\Repositories\Post;

use Illuminate\Pagination\LengthAwarePaginator;
use App\Models\Post;

interface PostRepositoryInterface
{
    public function paginateByUser(int $userId, array $filters = [], int $perPage = 15): LengthAwarePaginator;

    public function create(array $data): Post;

    public function findByIdAndUser(int $id, int $userId): ?Post;

    public function update(Post $post, array $data): Post;

    public function delete(Post $post): void;

    public function publish(Post $post): Post;
}