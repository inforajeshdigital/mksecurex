<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    protected $fillable = ['user_id', 'action', 'subject_type', 'subject_id', 'description', 'ip_address'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public static function record(string $action, string $description, $subject = null): void
    {
        try {
            static::create([
                'user_id' => auth()->id(),
                'action' => $action,
                'subject_type' => $subject ? class_basename($subject) : null,
                'subject_id' => $subject?->getKey(),
                'description' => mb_substr($description, 0, 250),
                'ip_address' => request()->ip(),
            ]);
        } catch (\Throwable $e) {
            report($e);
        }
    }
}
