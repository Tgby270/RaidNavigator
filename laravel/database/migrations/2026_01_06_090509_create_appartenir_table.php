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
        Schema::create('sae_appartenir', function (Blueprint $table) {
    $table->unsignedBigInteger('RAID_ID');
    $table->unsignedBigInteger('CRS_ID');
    $table->unsignedBigInteger('EQU_ID');
    $table->unsignedBigInteger('STU_ID'); // FK vers users
    $table->timestamps();

    // FK composite vers equipe
    $table->foreign(['RAID_ID', 'CRS_ID', 'EQU_ID'])
          ->references(['RAID_ID', 'CRS_ID', 'EQU_ID'])
          ->on('sae_equipe')
          ->onDelete('cascade');

    // FK vers student / users
    $table->foreign('STU_ID')->references('USE_ID')->on('sae_users')->onDelete('cascade');

    $table->engine = 'InnoDB';
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sae_appartenir');
    }
};
