<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasAdminAccess();
    }

    public function view(User $user, User $model): bool
    {
        return $user->hasAdminAccess() && $this->canManageUser($user, $model);
    }

    public function create(User $user): bool
    {
        return $user->hasAdminAccess();
    }

    public function update(User $user, User $model): bool
    {
        return $user->hasAdminAccess() && $this->canManageUser($user, $model);
    }

    public function delete(User $user, User $model): bool
    {
        return $user->hasAdminAccess() && $this->canManageUser($user, $model);
    }

    public function deleteAny(User $user): bool
    {
        return $user->hasAdminAccess();
    }

    protected function canManageUser(User $actor, User $target): bool
    {
        return $actor->isSuperadmin() || ! $target->isSuperadmin();
    }
}
