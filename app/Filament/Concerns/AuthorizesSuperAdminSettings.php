<?php

namespace App\Filament\Concerns;

use App\Models\User;

trait AuthorizesSuperAdminSettings
{
    protected function authorizeSuperAdminSettings(): void
    {
        abort_unless(
            auth()->user() instanceof User && auth()->user()->canAccessSuperAdminSettings(),
            403
        );
    }
}
