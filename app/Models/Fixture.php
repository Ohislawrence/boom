<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Fixture extends Model
{
    protected $fillable = [
        'api_football_id',
        'slug',
        'league_id',
        'home_team',
        'away_team',
        'home_team_api_id',
        'away_team_api_id',
        'home_logo',
        'away_logo',
        'match_date',
        'venue',
        'venue_city',
        'referee',
        'round',
        'season',
        'status',
        'score_home',
        'score_away',
        'halftime_home',
        'halftime_away',
        'home_odds',
        'draw_odds',
        'away_odds',
        'over25_odds',
        'under25_odds',
        'btts_yes_odds',
        'btts_no_odds',
        'prediction_winner',
        'prediction_percent_home',
        'prediction_percent_draw',
        'prediction_percent_away',
        'prediction_advice',
        'prediction_under_over',
        'raw_data',
        'analysis_run_at',
    ];

    protected $casts = [
        'match_date'              => 'datetime',
        'analysis_run_at'         => 'datetime',
        'raw_data'                => 'array',
        'home_odds'               => 'float',
        'draw_odds'               => 'float',
        'away_odds'               => 'float',
        'over25_odds'             => 'float',
        'under25_odds'            => 'float',
        'btts_yes_odds'           => 'float',
        'btts_no_odds'            => 'float',
        'prediction_percent_home' => 'integer',
        'prediction_percent_draw' => 'integer',
        'prediction_percent_away' => 'integer',
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

        static::creating(function (Fixture $fixture) {
            if (empty($fixture->slug)) {
                $base = Str::slug(
                    $fixture->home_team . ' vs ' . $fixture->away_team
                    . ' ' . \Carbon\Carbon::parse($fixture->match_date)->format('Y-m-d')
                );
                $slug = $base;
                $i    = 2;
                while (static::where('slug', $slug)->exists()) {
                    $slug = $base . '-' . $i++;
                }
                $fixture->slug = $slug;
            }
        });
    }

    // ── Relationships ──

    public function league(): BelongsTo
    {
        return $this->belongsTo(League::class);
    }

    public function tips(): HasMany
    {
        return $this->hasMany(Tip::class);
    }

    // ── Scopes ──

    public function scopeToday($query)
    {
        return $query->whereDate('match_date', today());
    }

    public function scopeUpcoming($query)
    {
        return $query->where('match_date', '>=', now())->where('status', 'NS');
    }

    public function scopeFinished($query)
    {
        return $query->where('status', 'FT');
    }
}
