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
        Schema::create('bookmaker_bet_market', function (Blueprint $table) {
            $table->foreignId('bookmaker_id')->constrained()->cascadeOnDelete();
            $table->foreignId('bet_market_id')->constrained()->cascadeOnDelete();
            $table->primary(['bookmaker_id', 'bet_market_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookmaker_bet_market');
    }
};
