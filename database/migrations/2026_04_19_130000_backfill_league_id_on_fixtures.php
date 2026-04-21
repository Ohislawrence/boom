<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Backfill league_id on fixtures that were imported before leagues were auto-created.
     * Uses the league_id (api_football_id), competition (name), country, and season
     * already stored in each fixture's raw_data JSON column.
     */
    public function up(): void
    {
        DB::table('fixtures')
            ->whereNull('league_id')
            ->whereNotNull('raw_data')
            ->orderBy('id')
            ->each(function ($fixture) {
                $raw = json_decode($fixture->raw_data, true);

                $apiLeagueId = $raw['league_id']   ?? null;
                $name        = $raw['competition']  ?? null;
                $country     = $raw['country']      ?? null;
                $season      = $raw['season']       ?? null;

                if (! $apiLeagueId) {
                    return;
                }

                // Find or create the league from the data we already have
                $league = DB::table('leagues')->where('api_football_id', $apiLeagueId)->first();

                if (! $league) {
                    $now = now();
                    DB::table('leagues')->insert([
                        'api_football_id' => $apiLeagueId,
                        'name'            => $name,
                        'country'         => $country,
                        'season'          => $season,
                        'is_active'       => false,
                        'created_at'      => $now,
                        'updated_at'      => $now,
                    ]);
                    $league = DB::table('leagues')->where('api_football_id', $apiLeagueId)->first();
                }

                if ($league) {
                    DB::table('fixtures')
                        ->where('id', $fixture->id)
                        ->update(['league_id' => $league->id]);
                }
            });
    }

    public function down(): void
    {
        // Cannot safely reverse — would need to know which league_ids were null before
    }
};
