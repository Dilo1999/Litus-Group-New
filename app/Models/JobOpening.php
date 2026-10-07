<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class JobOpening extends Model
{
    protected $fillable = [
        'title',
        'slug',
        'company',
        'location',
        'type',
        'department',
        'description',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    protected static function booted(): void
    {
        // The public /careers/{slug} URL is generated from the title (not editable in the admin).
        static::saving(function (JobOpening $job) {
            if (blank($job->slug) || $job->isDirty('title')) {
                $job->slug = static::uniqueSlug(Str::slug((string) $job->title) ?: 'job', $job->id);
            }
        });
    }

    public static function uniqueSlug(string $base, ?int $ignoreId = null): string
    {
        $slug = $base;
        for ($i = 2; static::query()->where('slug', $slug)->when($ignoreId, fn ($q) => $q->whereKeyNot($ignoreId))->exists(); $i++) {
            $slug = $base.'-'.$i;
        }

        return $slug;
    }
}
