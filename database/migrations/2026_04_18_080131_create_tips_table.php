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
        Schema::create('tips', function (Blueprint $table) {
            $table->id();
            $table->foreignId('fixture_id')->constrained()->cascadeOnDelete();
            $table->foreignId('bet_market_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('submitted_by')->nullable()->constrained('users')->nullOnDelete(); // null = AI
            $table->string('market');       // e.g. "Over 2.5 Goals"
            $table->string('selection');    // e.g. "Over 2.5"
            $table->decimal('odds', 6, 2)->nullable();
            $table->unsignedTinyInteger('confidence')->default(0); // 0–100
            $table->boolean('is_value_bet')->default(false);
            $table->boolean('is_ai_generated')->default(true);
            $table->text('reasoning')->nullable();
            $table->enum('status', ['pending', 'published', 'rejected'])->default('pending');
            $table->enum('result', ['pending', 'win', 'loss', 'void'])->default('pending');
            $table->timestamps();

            $table->index('status');
            $table->index('result');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tips');
    }
};
