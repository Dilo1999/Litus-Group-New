<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Filament\Models\Contracts\FilamentUser;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements FilamentUser
{
    use HasApiTokens, HasFactory, Notifiable;

    public const ROLE_SUPERADMIN = 'superadmin';

    public const ROLE_ADMIN = 'admin';

    public const ROLE_MANAGEMENT = 'management';

    public const ROLE_HR = 'hr';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function canAccessFilament(): bool
    {
        return in_array($this->role, [
            self::ROLE_SUPERADMIN,
            self::ROLE_ADMIN,
            self::ROLE_MANAGEMENT,
            self::ROLE_HR,
        ], true);
    }

    public function isSuperadmin(): bool
    {
        return $this->role === self::ROLE_SUPERADMIN;
    }

    public function isAdmin(): bool
    {
        return $this->role === self::ROLE_ADMIN;
    }

    /** Admin or Superadmin — full panel access except Super admin settings. */
    public function hasAdminAccess(): bool
    {
        return $this->isAdmin() || $this->isSuperadmin();
    }

    public function canAccessSuperAdminSettings(): bool
    {
        return $this->isSuperadmin();
    }

    public function isManagement(): bool
    {
        return $this->role === self::ROLE_MANAGEMENT;
    }

    public function isHr(): bool
    {
        return $this->role === self::ROLE_HR;
    }

    /**
     * @return array<string, string>
     */
    public static function roleOptions(): array
    {
        return [
            self::ROLE_HR => 'HR',
            self::ROLE_MANAGEMENT => 'Management',
            self::ROLE_ADMIN => 'Admin',
            self::ROLE_SUPERADMIN => 'Superadmin',
        ];
    }

    /**
     * Roles the given user may assign when creating or editing users.
     *
     * @return array<string, string>
     */
    public static function assignableRoleOptions(?User $actor): array
    {
        $options = self::roleOptions();

        if (! $actor?->isSuperadmin()) {
            unset($options[self::ROLE_SUPERADMIN]);
        }

        return $options;
    }

    /**
     * Users visible in admin lists — non-superadmins never see superadmin accounts.
     */
    public function scopeVisibleTo(Builder $query, ?User $actor): Builder
    {
        if ($actor?->isSuperadmin()) {
            return $query;
        }

        return $query->where('role', '!=', self::ROLE_SUPERADMIN);
    }
}
