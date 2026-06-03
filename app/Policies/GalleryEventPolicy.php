<?php

namespace App\Policies;

use App\Models\GalleryEvent;
use App\Models\User;

class GalleryEventPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasAdminAccess() || $user->isManagement();
    }

    public function view(User $user, GalleryEvent $model): bool
    {
        return $user->hasAdminAccess() || $user->isManagement();
    }

    public function create(User $user): bool
    {
        return $user->hasAdminAccess() || $user->isManagement();
    }

    public function update(User $user, GalleryEvent $model): bool
    {
        return $user->hasAdminAccess() || $user->isManagement();
    }

    public function delete(User $user, GalleryEvent $model): bool
    {
        return $user->hasAdminAccess() || $user->isManagement();
    }

    public function deleteAny(User $user): bool
    {
        return $user->hasAdminAccess() || $user->isManagement();
    }
}
