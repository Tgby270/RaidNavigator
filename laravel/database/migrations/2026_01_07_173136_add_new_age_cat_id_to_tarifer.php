<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('sae_tarifer', function (Blueprint $table) {
            // idempotent: only add AGE_ID if it doesn't already exist
            if (!Schema::hasColumn('sae_tarifer', 'AGE_ID')) {
                $table->unsignedBigInteger('AGE_ID')->nullable()->after('CRS_ID');
                $table->foreign('AGE_ID')->references('AGE_ID')->on('sae_cat_age')->onDelete('cascade');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sae_tarifer', function (Blueprint $table) {
            if (Schema::hasColumn('sae_tarifer', 'AGE_ID')) {
                $table->dropForeign(['AGE_ID']);
                $table->dropColumn('AGE_ID');
            }
        });
    }
};
