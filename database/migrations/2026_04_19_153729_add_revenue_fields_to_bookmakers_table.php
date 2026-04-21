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
        Schema::table('bookmakers', function (Blueprint $table) {
            $table->string('logo_url')->nullable()->after('logo');
            $table->decimal('cpa_value', 8, 2)->nullable()->after('sort_order');
            $table->decimal('revshare_percentage', 5, 2)->nullable();
            $table->string('conversion_optimization', 20)->nullable(); // 'cpa' or 'revshare'
            $table->unsignedInteger('click_count')->default(0);
            $table->unsignedInteger('conversion_count')->default(0);
            $table->decimal('revenue_generated', 10, 2)->default(0);
            $table->boolean('is_featured')->default(false);
            $table->json('key_features')->nullable();
            $table->boolean('fast_withdrawal')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bookmakers', function (Blueprint $table) {
            $table->dropColumn([
                'logo_url', 'cpa_value', 'revshare_percentage', 'conversion_optimization',
                'click_count', 'conversion_count', 'revenue_generated',
                'is_featured', 'key_features', 'fast_withdrawal',
            ]);
        });
    }
};
