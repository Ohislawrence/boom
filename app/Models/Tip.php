<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Str;

class Tip extends Model
{
    protected $fillable = [
        'fixture_id',
        'bet_market_id',
        'submitted_by',
        'slug',
        'market',
        'selection',
        'odds',
        'confidence',
        'is_value_bet',
        'is_ai_generated',
        'reasoning',
        'status',
        'result',
    ];

    protected $casts = [
        'odds'             => 'float',
        'confidence'       => 'integer',
        'is_value_bet'     => 'boolean',
        'is_ai_generated'  => 'boolean',
    ];

    // ── Route model binding ──

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    // ── Auto-generate slug on create ──

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (Tip $tip) {
            if (empty($tip->slug)) {
                $fixture = Fixture::find($tip->fixture_id);
                if ($fixture) {
                    $base = Str::slug(
                        $fixture->home_team . ' vs ' . $fixture->away_team
                        . ' ' . $tip->market
                        . ' ' . Carbon::parse($fixture->match_date)->format('Y-m-d')
                    );
                    $slug = $base;
                    $i    = 2;
                    while (static::where('slug', $slug)->exists()) {
                        $slug = $base . '-' . $i++;
                    }
                    $tip->slug = $slug;
                }
            }
        });
    }

    // ── Relationships ──

    public function fixture(): BelongsTo
    {
        return $this->belongsTo(Fixture::class);
    }

    public function betMarket(): BelongsTo
    {
        return $this->belongsTo(BetMarket::class);
    }

    public function submittedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'submitted_by');
    }

    public function tipResult(): HasOne
    {
        return $this->hasOne(TipResult::class);
    }

    // ── Scopes ──

    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeAiGenerated($query)
    {
        return $query->where('is_ai_generated', true);
    }

    public function scopeHighConfidence($query, int $threshold = 75)
    {
        return $query->where('confidence', '>=', $threshold);
    }
}
