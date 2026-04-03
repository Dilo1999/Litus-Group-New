<?php

namespace App\Policies;

use App\Models\BlogPost;
use App\Models\User;

class BlogPostPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdmin() || $user->isManagement();
    }

    public function view(User $user, BlogPost $model): bool
    {
        return $user->isAdmin() || $user->isManagement();
    }

    public function create(User $user): bool
    {
        return $user->isAdmin() || $user->isManagement();
    }

    public function update(User $user, BlogPost $model): bool
    {
        return $user->isAdmin() || $user->isManagement();
    }

    public function delete(User $user, BlogPost $model): bool
    {
        return $user->isAdmin() || $user->isManagement();
    }

    public function deleteAny(User $user): bool
    {
        return $user->isAdmin() || $user->isManagement();
    }
}

