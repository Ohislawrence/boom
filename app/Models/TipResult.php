<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TipResult extends Model
{
    protected $fillable = [
        'tip_id',
        'result',
        'closing_odds',
        'profit_loss',
        'notes',
        'resolved_at',
    ];

    protected $casts = [
        'closing_odds' => 'float',
        'profit_loss'  => 'float',
        'resolved_at'  => 'datetime',
    ];

    // ── Relationships ──

    public function tip(): BelongsTo
    {
        return $this->belongsTo(Tip::class);
    }
}
