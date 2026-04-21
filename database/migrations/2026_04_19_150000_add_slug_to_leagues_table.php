<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('leagues', function (Blueprint $table) {
            $table->string('slug')->nullable()->unique()->after('api_football_id');
        });

        // Backfill slugs for existing rows
        DB::table('leagues')->orderBy('id')->each(function ($league) {
            $base = Str::slug(
                $league->name . ($league->country ? '-' . $league->country : '')
            );
            $slug = $base;
            $i    = 2;
            while (DB::table('leagues')->where('slug', $slug)->exists()) {
                $slug = $base . '-' . $i++;
            }
            DB::table('leagues')->where('id', $league->id)->update(['slug' => $slug]);
        });
    }

    public function down(): void
    {
        Schema::table('leagues', function (Blueprint $table) {
            $table->dropColumn('slug');
        });
    }
};
