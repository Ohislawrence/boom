<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Role type chosen at registration
            $table->enum('registration_role', ['bettor', 'tipster'])->default('bettor')->after('email');

            // Tipster-specific fields
            $table->string('tipster_bio', 500)->nullable()->after('registration_role');
            $table->string('tipster_speciality', 150)->nullable()->after('tipster_bio'); // e.g. "Premier League, Over/Under"

            // Approval workflow — only relevant for tipsters
            $table->enum('approval_status', ['pending', 'approved', 'rejected'])->default('approved')->after('tipster_speciality');
            // bettors default to 'approved'; tipsters default to 'pending' (set in controller)
            $table->timestamp('approved_at')->nullable()->after('approval_status');
            $table->unsignedBigInteger('approved_by')->nullable()->after('approved_at');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['registration_role', 'tipster_bio', 'tipster_speciality', 'approval_status', 'approved_at', 'approved_by']);
        });
    }
};
