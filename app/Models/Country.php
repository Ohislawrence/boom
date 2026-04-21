<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Country extends Model
{
    protected $fillable = ['name', 'code', 'flag_url', 'is_active'];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function leagues(): HasMany
    {
        return $this->hasMany(League::class);
    }
}
