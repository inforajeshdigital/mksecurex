<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Faq extends Model
{
    protected $fillable = ['faqable_type', 'faqable_id', 'category', 'question', 'answer', 'sort_order', 'is_active'];

    protected $casts = ['is_active' => 'boolean'];

    public function faqable()
    {
        return $this->morphTo();
    }
}
