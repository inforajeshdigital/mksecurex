<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PageSection extends Model
{
    protected $fillable = ['page_id', 'type', 'name', 'data', 'sort_order', 'is_active'];

    protected $casts = ['data' => 'array', 'is_active' => 'boolean'];

    public function page()
    {
        return $this->belongsTo(Page::class);
    }

    public function get(string $key, $default = null)
    {
        $v = data_get($this->data, $key);

        return ($v === null || $v === '') ? $default : $v;
    }

    public function items(): array
    {
        return array_values(array_filter($this->data['items'] ?? [], fn ($i) => is_array($i) && array_filter($i, fn ($v) => $v !== null && $v !== '')));
    }

    public function getLabelAttribute(): string
    {
        return config("sections.types.{$this->type}.label", ucfirst(str_replace('_', ' ', $this->type)));
    }
}
