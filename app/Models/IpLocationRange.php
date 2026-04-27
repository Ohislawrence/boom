<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IpLocationRange extends Model
{
    protected $table = 'ip_location_ranges';

    public $timestamps = false;

    protected $fillable = [
        'ip_from',
        'ip_to',
        'country_code',
        'country_name',
        'timezone',
    ];
}
