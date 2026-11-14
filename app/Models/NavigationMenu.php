<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class NavigationMenu extends Model
{
    protected $fillable = ['name', 'location'];

    public const LOCATIONS = ['header' => 'Header Menu', 'footer_quick' => 'Footer Quick Links', 'footer_legal' => 'Footer Legal Links'];

    public function items()
    {
        return $this->hasMany(NavigationItem::class)->orderBy('sort_order');
    }

    public static function forLocation(string $location)
    {
        return Cache::rememberForever("menu.$location", function () use ($location) {
            $menu = static::where('location', $location)->first();
            if (! $menu) {
                return collect();
            }

            return $menu->items()->whereNull('parent_id')->where('is_active', true)
                ->with(['children' => fn ($q) => $q->where('is_active', true)])->get();
        });
    }

    public static function flushCache(): void
    {
        foreach (array_keys(self::LOCATIONS) as $loc) {
            Cache::forget("menu.$loc");
        }
    }
}
