<?php

namespace App\Filament\Concerns;

trait BlocksHrAccess
{
    protected function abortIfHr(): void
    {
        abort_if(auth()->user()?->isHr(), 403);
    }
}
