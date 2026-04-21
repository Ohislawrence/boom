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
        Schema::create('bet_markets', function (Blueprint $table) {
            $table->id();
            $table->string('name');               // e.g. "Match Winner (1X2)"
            $table->string('slug')->unique();      // e.g. "1x2"
            $table->string('category');            // e.g. "Result", "Goals", "Handicap"
            $table->text('description')->nullable(); // plain-language explanation for readers
            $table->boolean('is_active')->default(true);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bet_markets');
    }
};
