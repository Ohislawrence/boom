<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ip_location_ranges', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('ip_from')->index();
            $table->unsignedBigInteger('ip_to')->index();
            $table->string('country_code', 2)->index();
            $table->string('country_name', 120)->nullable();
            $table->string('timezone', 64)->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ip_location_ranges');
    }
};
