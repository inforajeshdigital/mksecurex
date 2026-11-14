<?php

namespace App\Models;

use App\Models\Concerns\HasSeo;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Page extends Model
{
    use HasSeo, SoftDeletes;

    protected $fillable = ['title', 'slug', 'template', 'excerpt', 'banner_image', 'banner_alt', 'show_banner', 'is_system', 'status', 'published_on', 'sort_order'];

    protected $casts = ['show_banner' => 'boolean', 'is_system' => 'boolean', 'published_on' => 'date'];

    public function sections()
    {
        return $this->hasMany(PageSection::class)->orderBy('sort_order');
    }

    public function activeSections()
    {
        return $this->sections()->where('is_active', true);
    }

    public function scopePublished($q)
    {
        return $q->where('status', 'published');
    }

    public function getUrlAttribute(): string
    {
        return $this->slug === 'home' ? url('/') : url($this->slug);
    }

    public function getHeroImageAttribute(): ?string
    {
        return ($this->show_banner ?? true) ? $this->banner_image : null;
    }
}
