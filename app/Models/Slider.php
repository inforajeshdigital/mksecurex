<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Slider extends Model
{
    protected $fillable = ['badge', 'heading', 'subheading', 'description', 'image', 'image_alt', 'btn1_text', 'btn1_url', 'btn2_text', 'btn2_url', 'sort_order', 'is_active'];

    protected $casts = ['is_active' => 'boolean'];
}
