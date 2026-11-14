<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;

class Setting extends Model
{
    protected $table = 'website_settings';

    protected $fillable = ['group', 'key', 'value'];

    public const CACHE_KEY = 'website_settings.all';

    protected static ?array $memo = null;

    public static function allCached(): array
    {
        if (static::$memo !== null) {
            return static::$memo;
        }

        return static::$memo = Cache::rememberForever(self::CACHE_KEY, function () {
            try {
                return Schema::hasTable('website_settings') ? static::pluck('value', 'key')->toArray() : [];
            } catch (\Throwable $e) {
                return [];
            }
        });
    }

    public static function get(string $key, $default = null)
    {
        $all = static::allCached();

        return array_key_exists($key, $all) && $all[$key] !== null && $all[$key] !== '' ? $all[$key] : $default;
    }

    public static function put(string $key, $value, string $group = 'general'): void
    {
        static::updateOrCreate(['key' => $key], ['value' => $value, 'group' => $group]);
    }

    public static function flush(): void
    {
        static::$memo = null;
        Cache::forget(self::CACHE_KEY);
    }

    protected static function booted(): void
    {
        static::saved(fn () => static::flush());
        static::deleted(fn () => static::flush());
    }
}
