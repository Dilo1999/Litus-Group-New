<?php

namespace App\Policies;

use App\Models\MediaAsset;
use App\Models\User;

class MediaAssetPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasAdminAccess() || $user->isManagement();
    }

    public function view(User $user, MediaAsset $model): bool
    {
        return $user->hasAdminAccess() || $user->isManagement();
    }

    public function create(User $user): bool
    {
        return $user->hasAdminAccess() || $user->isManagement();
    }

    public function update(User $user, MediaAsset $model): bool
    {
        return $user->hasAdminAccess() || $user->isManagement();
    }

    public function delete(User $user, MediaAsset $model): bool
    {
        return $user->hasAdminAccess() || $user->isManagement();
    }

    public function deleteAny(User $user): bool
    {
        return $user->hasAdminAccess() || $user->isManagement();
    }
}
