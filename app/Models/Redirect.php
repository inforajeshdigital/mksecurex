<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Redirect extends Model
{
    protected $fillable = ['from_path', 'to_url', 'type', 'is_active', 'hits', 'last_hit_at', 'notes'];

    protected $casts = ['is_active' => 'boolean', 'last_hit_at' => 'datetime'];

    public static function normalize(string $path): string
    {
        $p = parse_url(trim($path), PHP_URL_PATH) ?? '/';

        return '/'.trim(rawurldecode($p), '/');
    }

    public static function activeMap(): array
    {
        return Cache::rememberForever('redirects.map', function () {
            try {
                return static::where('is_active', true)->get(['id', 'from_path', 'to_url', 'type'])
                    ->mapWithKeys(fn ($r) => [$r->from_path => ['id' => $r->id, 'to' => $r->to_url, 'type' => $r->type]])->toArray();
            } catch (\Throwable $e) {
                return [];
            }
        });
    }

    protected static function booted(): void
    {
        static::saved(fn () => Cache::forget('redirects.map'));
        static::deleted(fn () => Cache::forget('redirects.map'));
    }
}
