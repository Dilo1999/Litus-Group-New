<?php

namespace App\Policies;

use App\Models\JobOpening;
use App\Models\User;

class JobOpeningPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdmin() || $user->isHr();
    }

    public function view(User $user, JobOpening $jobOpening): bool
    {
        return $user->isAdmin() || $user->isHr();
    }

    public function create(User $user): bool
    {
        return $user->isAdmin() || $user->isHr();
    }

    public function update(User $user, JobOpening $jobOpening): bool
    {
        return $user->isAdmin() || $user->isHr();
    }

    public function delete(User $user, JobOpening $jobOpening): bool
    {
        return $user->isAdmin() || $user->isHr();
    }

    public function deleteAny(User $user): bool
    {
        return $user->isAdmin() || $user->isHr();
    }
}
