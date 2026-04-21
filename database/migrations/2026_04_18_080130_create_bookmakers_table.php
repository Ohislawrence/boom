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
        Schema::create('bookmakers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('logo')->nullable();
            $table->string('affiliate_url');
            $table->string('welcome_offer')->nullable();  // e.g. "Bet £10 Get £30"
            $table->string('bonus_text')->nullable();     // short promo label
            $table->text('review')->nullable();           // full review HTML/markdown
            $table->decimal('rating', 3, 1)->default(0); // out of 10
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
        Schema::dropIfExists('bookmakers');
    }
};
