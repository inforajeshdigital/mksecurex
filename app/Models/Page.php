<?php

namespace App\Models;

use App\Models\Concerns\HasSeo;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Page extends Model
{
    use HasSeo, SoftDeletes;

    protected $fillable = ['title', 'slug', 'template', 'excerpt', 'banner_image', 'banner_alt', 'is_system', 'status', 'published_on', 'sort_order'];

    protected $casts = ['is_system' => 'boolean', 'published_on' => 'date'];

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
}
