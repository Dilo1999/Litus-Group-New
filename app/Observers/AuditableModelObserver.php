<?php

namespace App\Observers;

use App\Models\ActivityLog;
use App\Models\SiteSetting;
use App\Support\ActivityLogger;
use Illuminate\Database\Eloquent\Model;

class AuditableModelObserver
{
    public function created(Model $model): void
    {
        if ($model instanceof ActivityLog) {
            return;
        }

        $attrs = ActivityLogger::redactSensitive($model, $model->getAttributes());
        $subject = self::subjectLine($model);

        ActivityLogger::log(
            'created',
            "Created {$subject}",
            $model,
            ['attributes' => $attrs]
        );
    }

    public function updated(Model $model): void
    {
        if ($model instanceof ActivityLog) {
            return;
        }

        $changes = $model->getChanges();
        unset($changes['updated_at'], $changes['created_at']);

        if ($model instanceof \App\Models\User) {
            unset($changes['password'], $changes['remember_token']);
        }

        if ($changes === []) {
            return;
        }

        $changes = ActivityLogger::redactSensitive($model, $changes);
        $subject = self::subjectLine($model);
        $fieldsList = self::humanizeFieldList($model, array_keys($changes));

        ActivityLogger::log(
            'updated',
            "Updated {$subject} — {$fieldsList}",
            $model,
            ['changes' => $changes]
        );
    }

    public function deleted(Model $model): void
    {
        if ($model instanceof ActivityLog) {
            return;
        }

        $subject = self::subjectLine($model);

        ActivityLogger::log(
            'deleted',
            "Deleted {$subject}",
            $model,
            []
        );
    }

    private static function subjectLine(Model $model): string
    {
        $base = class_basename($model);
        $id = $model->getKey();

        if ($model instanceof SiteSetting) {
            return "{$base} «{$model->key}» (ID {$id})";
        }

        if (isset($model->title) && $model->title !== '') {
            return "{$base} «{$model->title}» (ID {$id})";
        }

        if (isset($model->name) && $model->name !== '') {
            return "{$base} «{$model->name}» (ID {$id})";
        }

        if (isset($model->email) && $model->email !== '') {
            return "{$base} «{$model->email}» (ID {$id})";
        }

        if (isset($model->slug) && $model->slug !== '') {
            return "{$base} slug «{$model->slug}» (ID {$id})";
        }

        return "{$base} (ID {$id})";
    }

    /**
     * @param  list<string>  $keys
     */
    private static function humanizeFieldList(Model $model, array $keys): string
    {
        if ($keys === []) {
            return 'no fields listed';
        }

        sort($keys);

        if ($model instanceof SiteSetting) {
            return 'changed: '.implode(', ', $keys);
        }

        return 'changed fields: '.implode(', ', $keys);
    }
}
