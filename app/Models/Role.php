<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $fillable = ['name', 'slug', 'permissions', 'description'];

    protected $casts = ['permissions' => 'array'];

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function allows(string $module): bool
    {
        $perms = $this->permissions ?? [];

        return in_array('*', $perms, true) || in_array($module, $perms, true);
    }
}
