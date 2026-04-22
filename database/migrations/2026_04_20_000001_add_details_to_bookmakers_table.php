<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bookmakers', function (Blueprint $table) {
            if (!Schema::hasColumn('bookmakers', 'min_deposit'))
                $table->string('min_deposit')->nullable()->after('fast_withdrawal');
            if (!Schema::hasColumn('bookmakers', 'withdrawal_time'))
                $table->string('withdrawal_time')->nullable()->after('min_deposit');
            if (!Schema::hasColumn('bookmakers', 'live_betting'))
                $table->boolean('live_betting')->default(true)->after('withdrawal_time');
            if (!Schema::hasColumn('bookmakers', 'mobile_app'))
                $table->boolean('mobile_app')->default(true)->after('live_betting');
            if (!Schema::hasColumn('bookmakers', 'license'))
                $table->string('license')->nullable()->after('mobile_app');
            if (!Schema::hasColumn('bookmakers', 'founded_year'))
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
