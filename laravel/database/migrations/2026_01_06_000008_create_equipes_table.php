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
        Schema::create('sae_equipe', function (Blueprint $table) {
    $table->unsignedBigInteger('RAID_ID'); // FK vers raids
    $table->unsignedBigInteger('CRS_ID');  // FK vers course
    $table->unsignedBigInteger('EQU_ID');  // identifiant local par course
    $table->unsignedBigInteger('USE_ID');  // FK vers users
    $table->unsignedBigInteger('RES_ID')->nullable(); // FK vers resultats (nullable si pas encore de résultat)
    
    $table->string('EQU_NOM');
    $table->string('EQU_IMAGE')->nullable();
    $table->boolean('EQU_EST_PAYEE')->nullable();
    $table->timestamps();

    // Clé primaire composite (unique par course)
    $table->primary(['RAID_ID', 'CRS_ID', 'EQU_ID']);

    // Clés étrangères
    $table->foreign(['RAID_ID', 'CRS_ID'])->references(['RAID_ID', 'CRS_ID'])->on('sae_course')->onDelete('cascade');
    $table->foreign('USE_ID')->references('USE_ID')->on('sae_users')->onDelete('cascade');
    $table->foreign('RES_ID')->references('RES_ID')->on('sae_resultats')->onDelete('cascade');

    $table->engine = 'InnoDB';
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sae_equipe');
    }
};


