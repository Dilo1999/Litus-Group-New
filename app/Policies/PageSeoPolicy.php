<?php

namespace App\Policies;

use App\Models\PageSeo;
use App\Models\User;

class PageSeoPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasAdminAccess();
    }

    public function view(User $user, PageSeo $pageSeo): bool
    {
        return $user->hasAdminAccess();
    }

    public function create(User $user): bool
    {
        return $user->hasAdminAccess();
    }

    public function update(User $user, PageSeo $pageSeo): bool
    {
        return $user->hasAdminAccess();
    }

    public function delete(User $user, PageSeo $pageSeo): bool
    {
        return $user->hasAdminAccess();
    }

    public function deleteAny(User $user): bool
    {
        return $user->hasAdminAccess();
    }
}
