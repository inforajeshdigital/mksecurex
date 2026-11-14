<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SeoMeta extends Model
{
    protected $table = 'seo_metadata';

    public const FIELDS = ['meta_title', 'meta_description', 'focus_keyword', 'secondary_keywords', 'canonical_url', 'robots_index', 'robots_follow',
        'og_title', 'og_description', 'og_image', 'og_url', 'og_type', 'twitter_card', 'twitter_title', 'twitter_description', 'twitter_image', 'schema_markup'];

    protected $fillable = self::FIELDS;

    protected $casts = ['robots_index' => 'boolean', 'robots_follow' => 'boolean'];

    public function seoable()
    {
        return $this->morphTo();
    }
}
