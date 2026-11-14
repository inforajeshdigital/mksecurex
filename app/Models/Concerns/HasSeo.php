<?php

namespace App\Models\Concerns;

use App\Models\SeoMeta;
use Illuminate\Support\Facades\Cache;

trait HasSeo
{
    public static function bootHasSeo(): void
    {
        static::forceDeleting(fn ($model) => $model->seo()->delete());
        static::saved(fn () => Cache::forget('sitemap.xml'));
        static::deleted(fn () => Cache::forget('sitemap.xml'));
    }

    public function seo()
    {
        return $this->morphOne(SeoMeta::class, 'seoable');
    }

    public function saveSeo(array $data): void
    {
        $clean = array_intersect_key($data, array_flip(SeoMeta::FIELDS));
        $clean['robots_index'] = ! empty($data['robots_index']);
        $clean['robots_follow'] = ! empty($data['robots_follow']);
        $clean['og_type'] = $clean['og_type'] ?? 'website';
        $clean['twitter_card'] = $clean['twitter_card'] ?? 'summary_large_image';
        $this->seo()->updateOrCreate([], $clean);
        $this->unsetRelation('seo');
    }

    public function isIndexable(): bool
    {
        return $this->seo === null || $this->seo->robots_index;
    }
}
