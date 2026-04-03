<?php

namespace App\Policies;

use App\Models\Company;
use App\Models\User;

class CompanyPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdmin() || $user->isManagement();
    }

    public function view(User $user, Company $company): bool
    {
        return $user->isAdmin() || $user->isManagement();
    }

    public function create(User $user): bool
    {
        return $user->isAdmin() || $user->isManagement();
    }

    public function update(User $user, Company $company): bool
    {
        return $user->isAdmin() || $user->isManagement();
    }

    public function delete(User $user, Company $company): bool
    {
        return $user->isAdmin() || $user->isManagement();
    }

    public function deleteAny(User $user): bool
    {
        return $user->isAdmin() || $user->isManagement();
    }
}
