<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    protected $fillable = ['role_id', 'name', 'email', 'phone', 'avatar', 'is_active', 'password', 'last_login_at', 'last_login_ip'];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'last_login_at' => 'datetime',
            'is_active' => 'boolean',
            'password' => 'hashed',
        ];
    }

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function isSuperAdmin(): bool
    {
        return $this->role?->slug === 'super-admin';
    }

    public function canAccess(string $module): bool
    {
        return $this->isSuperAdmin() || (bool) $this->role?->allows($module);
    }
}
