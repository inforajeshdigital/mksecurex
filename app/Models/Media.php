<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Media extends Model
{
    protected $table = 'media';

    protected $fillable = ['file_name', 'path', 'original_path', 'mime_type', 'size', 'width', 'height', 'title', 'alt', 'caption', 'description', 'uploaded_by'];

    public function getUrlAttribute(): string
    {
        return asset($this->path);
    }

    public function getHumanSizeAttribute(): string
    {
        $s = (float) $this->size;
        foreach (['B', 'KB', 'MB', 'GB'] as $u) {
            if ($s < 1024) {
                return round($s, 1).' '.$u;
            }
            $s /= 1024;
        }

        return round($s, 1).' TB';
    }

    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
}
