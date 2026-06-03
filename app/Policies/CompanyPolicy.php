<?php

namespace App\Policies;

use App\Models\Company;
use App\Models\User;

class CompanyPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasAdminAccess();
    }

    public function view(User $user, Company $company): bool
    {
        return $user->hasAdminAccess();
    }

    public function create(User $user): bool
    {
        return $user->hasAdminAccess();
    }

    public function update(User $user, Company $company): bool
    {
        return $user->hasAdminAccess();
    }

    public function delete(User $user, Company $company): bool
    {
        return $user->hasAdminAccess();
    }

    public function deleteAny(User $user): bool
    {
        return $user->hasAdminAccess();
    }
}
