<?php

namespace App\Repositories\Platform;

use App\Models\Platform;
use Illuminate\Foundation\Auth\User;

class PlatformRepository implements PlatformRepositoryInterface
{
    public function allWithUserStatus(User $user)
    {
        $platforms = Platform::with(['users' => function ($query) use ($user) {
            $query->where('user_id', $user->id);
        }])->get();

        return $platforms->map(function ($platform) {
            $active = false;
            if ($platform->users->isNotEmpty()) {
                $active = $platform->users->first()->pivot->enabled;
            }
            return [
                'id' => $platform->id,
                'name' => $platform->name,
                'type' => $platform->type,
                'active' => $active,
            ];
        });
    }

    public function create(array $data): Platform
    {
        return Platform::create($data);
    }

    public function find(int $id): ?Platform
    {
        return Platform::find($id);
    }

    public function update(int $id, array $data): ?Platform
    {
        $platform = $this->find($id);
        if (!$platform) {
            return null;
        }
        $platform->update($data);
        return $platform;
    }

    public function delete(int $id): bool
    {
        $platform = $this->find($id);
        if (!$platform) {
            return false;
        }
        return $platform->delete();
    }

    public function toggleUserPlatform(User $user, int $platformId, bool $enabled): void
    {
        if ($enabled) {
            $user->platforms()->syncWithoutDetaching([
                $platformId => ['enabled' => true]
            ]);
        } else {
            $user->platforms()->updateExistingPivot($platformId, ['enabled' => false]);
        }
    }
}