<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bookmakers', function (Blueprint $table) {
            $table->string('min_deposit')->nullable()->after('fast_withdrawal');        // e.g. "₦100"
            $table->string('withdrawal_time')->nullable()->after('min_deposit');        // e.g. "Instant"
            $table->boolean('live_betting')->default(true)->after('withdrawal_time');
            $table->boolean('mobile_app')->default(true)->after('live_betting');
            $table->string('license')->nullable()->after('mobile_app');                // e.g. "NLRC"
            $table->unsignedSmallInteger('founded_year')->nullable()->after('license');
        });
    }

    public function down(): void
    {
        Schema::table('bookmakers', function (Blueprint $table) {
            $table->dropColumn(['min_deposit', 'withdrawal_time', 'live_betting', 'mobile_app', 'license', 'founded_year']);
        });
    }
};
