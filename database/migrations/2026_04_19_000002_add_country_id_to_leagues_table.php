<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('leagues', function (Blueprint $table) {
            // Nullable — no hard FK so API-Football "World" leagues don't break
            $table->unsignedBigInteger('country_id')->nullable()->after('country')->index();
        });
    }

    public function down(): void
    {
        Schema::table('leagues', function (Blueprint $table) {
            $table->dropIndex(['country_id']);
            $table->dropColumn('country_id');
        });
    }
};
