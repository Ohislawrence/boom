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
        Schema::table('fixtures', function (Blueprint $table) {
            $table->string('slug')->nullable()->unique()->after('api_football_id');
        });

        // Populate slugs for existing rows
        DB::table('fixtures')->orderBy('id')->each(function ($fixture) {
            $base = Str::slug(
                $fixture->home_team . ' vs ' . $fixture->away_team
                . ' ' . \Carbon\Carbon::parse($fixture->match_date)->format('Y-m-d')
            );
            $slug = $base;
            $i    = 2;
            while (DB::table('fixtures')->where('slug', $slug)->exists()) {
                $slug = $base . '-' . $i++;
            }
            DB::table('fixtures')->where('id', $fixture->id)->update(['slug' => $slug]);
        });
    }

    public function down(): void
    {
        Schema::table('fixtures', function (Blueprint $table) {
            $table->dropColumn('slug');
        });
    }
};
