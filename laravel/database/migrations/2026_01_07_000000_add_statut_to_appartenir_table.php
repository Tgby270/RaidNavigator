<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sae_appartenir', function (Blueprint $table) {
            $table->enum('APP_STATUT', ['en_attente', 'accepte', 'refuse'])->default('en_attente')->after('EQU_ID');
        });
    }

    public function down(): void
    {
        Schema::table('sae_appartenir', function (Blueprint $table) {
            $table->dropColumn('APP_STATUT');
        });
    }
};
