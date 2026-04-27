<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bookmaker_country', function (Blueprint $table) {
            $table->foreignId('bookmaker_id')->constrained()->cascadeOnDelete();
            $table->foreignId('country_id')->constrained()->cascadeOnDelete();
            $table->primary(['bookmaker_id', 'country_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bookmaker_country');
    }
};
