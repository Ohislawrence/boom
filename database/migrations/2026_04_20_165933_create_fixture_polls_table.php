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
        Schema::create('fixture_polls', function (Blueprint $table) {
            $table->id();
            $table->foreignId('fixture_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('home_votes')->default(0);
            $table->unsignedInteger('draw_votes')->default(0);
            $table->unsignedInteger('away_votes')->default(0);
            $table->timestamps();
        });

        // Tracks which IPs/sessions have already voted — prevents duplicate votes
        Schema::create('fixture_poll_votes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('fixture_id')->constrained()->cascadeOnDelete();
            $table->string('voter_hash', 64)->index(); // SHA-256(IP + UA + fixture_id)
            $table->enum('choice', ['home', 'draw', 'away']);
            $table->timestamp('voted_at')->useCurrent();

            $table->unique(['fixture_id', 'voter_hash']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fixture_poll_votes');
        Schema::dropIfExists('fixture_polls');
    }
};
