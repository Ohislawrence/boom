<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('fixtures', function (Blueprint $table) {
            // Match metadata
            $table->string('referee')->nullable()->after('venue');
            $table->string('venue_city')->nullable()->after('referee');
            $table->string('round')->nullable()->after('venue_city'); // e.g. "Regular Season - 30"

            // Halftime score (for finished matches)
            $table->unsignedTinyInteger('halftime_home')->nullable()->after('score_away');
            $table->unsignedTinyInteger('halftime_away')->nullable()->after('halftime_home');

            // 1X2 opening odds (from API-Football /odds)
            $table->decimal('home_odds', 6, 2)->nullable()->after('halftime_away');
            $table->decimal('draw_odds', 6, 2)->nullable()->after('home_odds');
            $table->decimal('away_odds', 6, 2)->nullable()->after('draw_odds');

            // Over/Under 2.5 odds
            $table->decimal('over25_odds', 6, 2)->nullable()->after('away_odds');
            $table->decimal('under25_odds', 6, 2)->nullable()->after('over25_odds');

            // BTTS odds
            $table->decimal('btts_yes_odds', 6, 2)->nullable()->after('under25_odds');
            $table->decimal('btts_no_odds', 6, 2)->nullable()->after('btts_yes_odds');

            // API-Football /predictions
            $table->string('prediction_winner')->nullable()->after('btts_no_odds');   // team name
            $table->unsignedTinyInteger('prediction_percent_home')->nullable()->after('prediction_winner');
            $table->unsignedTinyInteger('prediction_percent_draw')->nullable()->after('prediction_percent_home');
            $table->unsignedTinyInteger('prediction_percent_away')->nullable()->after('prediction_percent_draw');
            $table->string('prediction_advice')->nullable()->after('prediction_percent_away');
            $table->string('prediction_under_over')->nullable()->after('prediction_advice'); // e.g. "+2.5" or "-2.5"
        });
    }

    public function down(): void
    {
        Schema::table('fixtures', function (Blueprint $table) {
            $table->dropColumn([
                'referee', 'venue_city', 'round',
                'halftime_home', 'halftime_away',
                'home_odds', 'draw_odds', 'away_odds',
                'over25_odds', 'under25_odds',
                'btts_yes_odds', 'btts_no_odds',
                'prediction_winner',
                'prediction_percent_home', 'prediction_percent_draw', 'prediction_percent_away',
                'prediction_advice', 'prediction_under_over',
            ]);
        });
    }
};
