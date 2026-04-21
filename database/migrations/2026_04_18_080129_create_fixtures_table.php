<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('fixtures', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('api_football_id')->unique();
            $table->foreignId('league_id')->nullable()->constrained()->nullOnDelete();
            $table->string('home_team');
            $table->string('away_team');
            $table->unsignedBigInteger('home_team_api_id');
            $table->unsignedBigInteger('away_team_api_id');
            $table->string('home_logo')->nullable();
            $table->string('away_logo')->nullable();
            $table->dateTime('match_date');
            $table->string('venue')->nullable();
            $table->unsignedSmallInteger('season');
            $table->string('status')->default('NS'); // NS, FT, PST, etc.
            $table->unsignedTinyInteger('score_home')->nullable();
            $table->unsignedTinyInteger('score_away')->nullable();
            $table->json('raw_data')->nullable(); // full API response cache
            $table->timestamp('analysis_run_at')->nullable();
            $table->timestamps();

            $table->index('match_date');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fixtures');
    }
};
