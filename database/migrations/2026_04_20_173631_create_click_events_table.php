<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('click_events', function (Blueprint $table) {
            $table->id();
            $table->string('event_type', 20)->index();   // affiliate | nav | cta | external | other
            $table->string('label', 250)->nullable();     // human-readable label passed from JS
            $table->string('target_url', 500)->nullable();
            $table->string('page_url', 500)->nullable();
            $table->string('referrer', 500)->nullable();  // document.referrer
            $table->char('country_code', 2)->nullable()->index();
            $table->string('country_name', 100)->nullable();
            $table->char('ip_hash', 64)->nullable();      // sha256 — no raw IP stored
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('device_type', 10)->nullable(); // mobile | tablet | desktop
            $table->timestamp('created_at')->useCurrent()->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('click_events');
    }
};
