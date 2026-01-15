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
        Schema::create('sae_resultats', function (Blueprint $table) {
            $table->id('RES_ID');
            $table->timestamps();
            $table->integer('RES_TEMPS');
            $table->integer('RES_POINTS');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sae_resultats');
    }
};
