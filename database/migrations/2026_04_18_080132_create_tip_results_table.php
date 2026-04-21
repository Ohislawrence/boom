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
        Schema::create('tip_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tip_id')->unique()->constrained()->cascadeOnDelete();
            $table->enum('result', ['win', 'loss', 'void']);
            $table->decimal('closing_odds', 6, 2)->nullable();
            $table->decimal('profit_loss', 8, 2)->nullable(); // based on 1 unit stake
            $table->text('notes')->nullable();
            $table->timestamp('resolved_at');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tip_results');
    }
};
