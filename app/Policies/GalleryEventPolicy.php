<?php

namespace App\Policies;

use App\Models\GalleryEvent;
use App\Models\User;

class GalleryEventPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdmin();
    }

    public function view(User $user, GalleryEvent $model): bool
    {
        return $user->isAdmin();
    }

    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    public function update(User $user, GalleryEvent $model): bool
    {
        return $user->isAdmin();
    }

    public function delete(User $user, GalleryEvent $model): bool
    {
        return $user->isAdmin();
    }
}
