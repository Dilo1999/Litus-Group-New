<?php

namespace App\Support;

use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;
use Throwable;

class ActivityLogger
{
    public static function log(
        string $event,
        string $description,
        ?Model $subject = null,
        array $properties = [],
        string $logName = 'filament'
    ): void {
        try {
            $user = Auth::user();
            $userId = $user instanceof User ? $user->id : null;
            $actorName = $user instanceof User ? (string) $user->name : null;
            $actorEmail = $user instanceof User ? (string) $user->email : null;

            $payload = array_merge($properties, ['description' => $description]);

            ActivityLog::query()->create([
                'user_id' => $userId,
                'actor_name' => $actorName,
                'actor_email' => $actorEmail,
                'log_name' => $logName,
                'event' => $event,
                'description' => $description,
                'subject_type' => $subject ? $subject->getMorphClass() : null,
                'subject_id' => $subject?->getKey(),
                'properties' => $payload,
                'ip_address' => Request::ip(),
                'user_agent' => Request::userAgent(),
            ]);
        } catch (Throwable $e) {
            report($e);
        }
    }

    /**
     * @param  array<string, mixed>  $attributes
     * @return array<string, mixed>
     */
    public static function redactSensitive(Model $model, array $attributes): array
    {
        if ($model instanceof User) {
            if (array_key_exists('password', $attributes)) {
                $attributes['password'] = '[redacted]';
            }
            if (array_key_exists('remember_token', $attributes)) {
                $attributes['remember_token'] = '[redacted]';
            }
        }

        return $attributes;
    }
}
