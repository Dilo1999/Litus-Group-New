<?php

namespace App\Filament\Resources\UserResource\Pages\Concerns;

use App\Models\User;

trait GuardsAssignableUserRole
{
    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function guardAssignableRoleInData(array $data): array
    {
        $role = $data['role'] ?? null;
        $allowed = array_keys(User::assignableRoleOptions(auth()->user()));

        abort_unless(is_string($role) && in_array($role, $allowed, true), 403);

        return $data;
    }
}
