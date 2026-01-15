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
        Schema::create('sae_course', function (Blueprint $table) {
    $table->unsignedBigInteger('RAID_ID'); // FK vers raids
    $table->unsignedBigInteger('CRS_ID');  // identifiant local au raid
    $table->unsignedBigInteger('USE_ID');  // FK vers users

    $table->string('CRS_NOM');
    $table->string('CRS_TYPE');
    $table->integer('CRS_DUREE');
    $table->integer('CRS_REDUC_LICENCIE')->nullable();
    $table->dateTime('CRS_DATE_HEURE_DEPART');
    $table->dateTime('CRS_DATE_HEURE_FIN');
    $table->integer('CRS_MIN_PARTICIPANTS');
    $table->integer('CRS_MAX_PARTICIPANTS');
    $table->integer('CRS_NB_EQUIPE_MIN');
    $table->integer('CRS_NB_EQUIPE_MAX');
    $table->integer('CRS_MAX_PARTICIPANTS_EQUIPE');
    $table->integer('CRS_PRIX_REPAS')->nullable();
    $table->string('CRS_DIFFICULTE')->nullable();
    $table->timestamps();

    // Clé primaire composite
    $table->primary(['RAID_ID', 'CRS_ID']);

    // Clés étrangères
    $table->foreign('RAID_ID')->references('RAID_ID')->on('sae_raids')->onDelete('cascade');
    $table->foreign('USE_ID')->references('USE_ID')->on('sae_users')->onDelete('cascade');

    $table->engine = 'InnoDB';
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sae_course');
    }
};
