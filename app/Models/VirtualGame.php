<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VirtualGame extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'tagline',
        'provider',
        'volatility',
        'rtp',
        'icon',
        'color',
        'description',
        'script_path',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function getScriptUrlAttribute(): ?string
    {
        if (empty($this->script_path)) {
            return null;
        }

        return asset('storage/' . $this->script_path);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
