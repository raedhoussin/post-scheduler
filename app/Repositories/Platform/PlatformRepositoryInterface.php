<?php

namespace App\Repositories\Platform;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use App\Models\Platform;
use Illuminate\Foundation\Auth\User;

interface PlatformRepositoryInterface
{
    public function allWithUserStatus(User $user);

    public function create(array $data): Platform;

    public function find(int $id): ?Platform;

    public function update(int $id, array $data): ?Platform;

    public function delete(int $id): bool;

    public function toggleUserPlatform(User $user, int $platformId, bool $enabled): void;
}