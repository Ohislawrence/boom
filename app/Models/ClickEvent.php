<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClickEvent extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'event_type',
        'label',
        'target_url',
        'page_url',
        'referrer',
        'country_code',
        'country_name',
        'ip_hash',
        'user_id',
        'device_type',
        'created_at',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
