<?php

namespace App\Policies;

use App\Models\TeamMember;
use App\Models\User;

class TeamMemberPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasAdminAccess();
    }

    public function view(User $user, TeamMember $teamMember): bool
    {
        return $user->hasAdminAccess();
    }

    public function create(User $user): bool
    {
        return $user->hasAdminAccess();
    }

    public function update(User $user, TeamMember $teamMember): bool
    {
        return $user->hasAdminAccess();
    }

    public function delete(User $user, TeamMember $teamMember): bool
    {
        return $user->hasAdminAccess();
    }

    public function deleteAny(User $user): bool
    {
        return $user->hasAdminAccess();
    }
}
