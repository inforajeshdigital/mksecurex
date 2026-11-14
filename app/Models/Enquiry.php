<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Enquiry extends Model
{
    use SoftDeletes;

    public const STATUSES = ['new' => 'New', 'contacted' => 'Contacted', 'converted' => 'Converted', 'closed' => 'Closed'];

    protected $fillable = ['full_name', 'company_name', 'phone', 'email', 'service_id', 'service_name', 'workforce_size', 'location', 'subject',
        'message', 'consent', 'form_type', 'source_url', 'status', 'is_spam', 'notes', 'ip_address', 'user_agent', 'read_at'];

    protected $casts = ['consent' => 'boolean', 'is_spam' => 'boolean', 'read_at' => 'datetime'];

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function getStatusColorAttribute(): string
    {
        return ['new' => 'primary', 'contacted' => 'info', 'converted' => 'success', 'closed' => 'secondary'][$this->status] ?? 'secondary';
    }
}
