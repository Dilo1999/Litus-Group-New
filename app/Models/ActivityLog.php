<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ActivityLog extends Model
{
    public $timestamps = false;

    public const UPDATED_AT = null;

    protected $fillable = [
        'user_id',
        'actor_name',
        'actor_email',
        'log_name',
        'event',
        'description',
        'subject_type',
        'subject_id',
        'properties',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'properties' => 'array',
        'created_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /** Who performed the action, as recorded at log time (survives user rename/delete). */
    public function getActorLabelAttribute(): string
    {
        if (filled($this->actor_name) || filled($this->actor_email)) {
            $parts = array_filter([$this->actor_name, $this->actor_email]);

            return $parts !== [] ? implode(' · ', $parts) : '—';
        }

        if ($this->relationLoaded('user') && $this->user) {
            return $this->user->name.' ('.$this->user->email.')';
        }

        if ($this->user_id) {
            return 'User #'.$this->user_id;
        }

        return 'System';
    }
}
