<?php

namespace App\Listeners;

use App\Models\User;
use App\Support\ActivityLogger;
use Illuminate\Auth\Events\Login;

class LogFilamentLogin
{
    public function handle(Login $event): void
    {
        if (! $event->user instanceof User) {
            return;
        }

        $panel = trim((string) config('filament.path', 'admin'), '/');
        $path = request()->path();

        if ($panel !== '' && ! str_starts_with($path, $panel)) {
            return;
        }

        ActivityLogger::log(
            'login',
            'Signed in to the Filament admin panel as '.$event->user->email,
            $event->user,
            ['guard' => $event->guard, 'user_id' => $event->user->id]
        );
    }
}
