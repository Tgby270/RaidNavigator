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
        Schema::table('sae_clubs', function (Blueprint $table) {
            $table->string('CLU_CONTACT')->nullable()->after('CLU_CODE_POSTAL');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sae_clubs', function (Blueprint $table) {
            $table->dropColumn('CLU_CONTACT');
        });
    }
};
