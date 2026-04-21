<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Bookmaker extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'logo',
        'logo_url',
        'affiliate_url',
        'welcome_offer',
        'bonus_text',
        'review',
        'rating',
        'is_active',
        'sort_order',
        'cpa_value',
        'revshare_percentage',
        'conversion_optimization',
        'click_count',
        'conversion_count',
        'revenue_generated',
        'is_featured',
        'key_features',
        'fast_withdrawal',
        'min_deposit',
        'withdrawal_time',
        'live_betting',
        'mobile_app',
        'license',
        'founded_year',
    ];

    protected $casts = [
        'rating'            => 'float',
        'is_active'         => 'boolean',
        'is_featured'       => 'boolean',
        'fast_withdrawal'   => 'boolean',
        'live_betting'      => 'boolean',
        'mobile_app'        => 'boolean',
        'key_features'      => 'array',
        'cpa_value'         => 'float',
        'revenue_generated' => 'float',
    ];

    /** Resolve logo from logo_url column or fall back to logo column */
    public function getLogoUrlAttribute(): ?string
    {
        return $this->attributes['logo_url'] ?? $this->attributes['logo'] ?? null;
    }

    // ── Relationships ──

    public function betMarkets(): BelongsToMany
    {
        return $this->belongsToMany(BetMarket::class, 'bookmaker_bet_market');
    }

    // ── Scopes ──

    public function scopeActive($query)
    {
        return $query->where('is_active', true)->orderBy('sort_order');
    }
}
