<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NavigationItem extends Model
{
    protected $fillable = ['navigation_menu_id', 'parent_id', 'label', 'url', 'type', 'new_tab', 'sort_order', 'is_active'];

    protected $casts = ['new_tab' => 'boolean', 'is_active' => 'boolean'];

    public const TYPES = ['custom' => 'Custom link', 'services' => 'Services dropdown (auto)', 'industries' => 'Industries dropdown (auto)'];

    public function menu()
    {
        return $this->belongsTo(NavigationMenu::class, 'navigation_menu_id');
    }

    public function children()
    {
        return $this->hasMany(NavigationItem::class, 'parent_id')->orderBy('sort_order');
    }

    public function getHrefAttribute(): string
    {
        return link_url($this->url ?: '#');
    }

    protected static function booted(): void
    {
        static::saved(fn () => NavigationMenu::flushCache());
        static::deleted(fn () => NavigationMenu::flushCache());
    }
}
