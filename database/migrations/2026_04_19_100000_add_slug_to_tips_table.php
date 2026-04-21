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
        Schema::table('tips', function (Blueprint $table) {
            $table->string('slug')->nullable()->unique()->after('id');
        });

        // Backfill slugs for existing tips
        $rows = DB::table('tips')
            ->join('fixtures', 'tips.fixture_id', '=', 'fixtures.id')
            ->select('tips.id', 'tips.market', 'fixtures.home_team', 'fixtures.away_team', 'fixtures.match_date')
            ->get();

        foreach ($rows as $row) {
            $base = Str::slug(
                $row->home_team . ' vs ' . $row->away_team
                . ' ' . $row->market
                . ' ' . \Carbon\Carbon::parse($row->match_date)->format('Y-m-d')
            );
            $slug = $base;
            $i    = 2;
            while (DB::table('tips')->where('slug', $slug)->exists()) {
                $slug = $base . '-' . $i++;
            }
            DB::table('tips')->where('id', $row->id)->update(['slug' => $slug]);
        }
    }

    public function down(): void
    {
        Schema::table('tips', function (Blueprint $table) {
            $table->dropColumn('slug');
        });
    }
};
