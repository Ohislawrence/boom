<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class League extends Model
{
    protected $fillable = [
        'api_football_id',
        'slug',
        'name',
        'country',
        'country_id',
        'logo_url',
        'season',
        'is_active',
    ];

    /**
     * API-Football country name → canonical Country name in our DB.
     * Add entries here as new mismatches are discovered.
     */
    public const COUNTRY_ALIASES = [
        'World'                => null,      // intentionally unmapped (global competitions)
        'Korea Republic'       => 'South Korea',
        'Korea DPR'            => 'North Korea',
        'Ivory Coast'          => 'Côte d\'Ivoire',
        'Cape Verde Islands'   => 'Cabo Verde',
        'Czech-Republic'       => 'Czech Republic',
        'Chinese Taipei'       => 'Taiwan',
        'USA'                  => 'United States',
        'England'              => 'England',
        'Scotland'             => 'Scotland',
        'Wales'                => 'Wales',
        'Northern-Ireland'     => 'Northern Ireland',
    ];

    protected static function boot(): void
    {
        parent::boot();

        static::saving(function (League $league) {
            // Only resolve if country_id is not already set
            if (! $league->country_id && $league->country) {
                $league->country_id = static::resolveCountryId($league->country);
            }
        });

        static::creating(function (League $league) {
            if (empty($league->slug)) {
                $base = Str::slug(
                    $league->name . ($league->country ? '-' . $league->country : '')
                );
                $slug = $base;
                $i    = 2;
                while (static::where('slug', $slug)->exists()) {
                    $slug = $base . '-' . $i++;
                }
                $league->slug = $slug;
            }
        });
    }

    public static function resolveCountryId(string $apiCountryName): ?int
    {
        // Direct alias override
        if (array_key_exists($apiCountryName, static::COUNTRY_ALIASES)) {
            $mapped = static::COUNTRY_ALIASES[$apiCountryName];
            if ($mapped === null) return null; // explicitly unmapped (e.g. World)
            return Country::where('name', $mapped)->value('id');
        }

        // Try exact match first, then case-insensitive
        return Country::where('name', $apiCountryName)->value('id')
            ?? Country::whereRaw('LOWER(name) = ?', [strtolower($apiCountryName)])->value('id');
    }

    // ── Route model binding ──

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // ── Relationships ──

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    public function fixtures(): HasMany
    {
        return $this->hasMany(Fixture::class);
    }

    // ── Scopes ──

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
