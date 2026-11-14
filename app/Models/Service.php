<?php

namespace App\Models;

use App\Models\Concerns\HasSeo;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Service extends Model
{
    use HasSeo, SoftDeletes;

    protected $fillable = ['title', 'slug', 'icon', 'short_description', 'intro', 'description', 'features', 'benefits', 'process',
        'image', 'banner_image', 'image_alt', 'sort_order', 'status'];

    protected $casts = ['features' => 'array', 'benefits' => 'array', 'process' => 'array'];

    public function scopePublished($q)
    {
        return $q->where('status', 'published');
    }

    public function scopeOrdered($q)
    {
        return $q->orderBy('sort_order')->orderBy('id');
    }

    public function industries()
    {
        return $this->belongsToMany(Industry::class);
    }

    public function related()
    {
        return $this->belongsToMany(Service::class, 'service_related', 'service_id', 'related_id');
    }

    public function faqs()
    {
        return $this->morphMany(Faq::class, 'faqable')->orderBy('sort_order');
    }

    public function getUrlAttribute(): string
    {
        return route('services.show', $this->slug);
    }
}
