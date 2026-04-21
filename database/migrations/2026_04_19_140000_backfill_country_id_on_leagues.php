<?php

use App\Models\League;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Resolve country_id on leagues that were inserted via DB::table() (bypassing Eloquent boot events).
     */
    public function up(): void
    {
        League::whereNull('country_id')
            ->whereNotNull('country')
            ->each(function (League $league) {
                // Trigger the saving boot event which calls resolveCountryId()
                $league->save();
            });
    }

    public function down(): void
    {
        // Not reversible
    }
};
