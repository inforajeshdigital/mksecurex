<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Slider extends Model
{
    protected $fillable = ['badge', 'heading', 'subheading', 'description', 'image', 'image_alt', 'show_background', 'show_side_image', 'btn1_text', 'btn1_url', 'btn2_text', 'btn2_url', 'sort_order', 'is_active'];

    protected $casts = ['is_active' => 'boolean', 'show_background' => 'boolean', 'show_side_image' => 'boolean'];

    public function getBackgroundImageAttribute(): ?string
    {
        return $this->image && ($this->show_background ?? true) ? $this->image : null;
    }

    public function getSideImageAttribute(): ?string
    {
        return $this->image && ($this->show_side_image ?? false) ? $this->image : null;
    }
}
