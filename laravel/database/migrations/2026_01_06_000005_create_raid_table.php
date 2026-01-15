<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sae_raids', function (Blueprint $table) {
            $table->id('RAID_ID'); // BIGINT UNSIGNED AUTO_INCREMENT
            $table->unsignedBigInteger('USE_ID'); // FK vers users
            $table->unsignedBigInteger('CLU_ID'); // FK vers clubs
            $table->string('raid_nom');
            $table->date('raid_date_debut');
            $table->date('raid_date_fin');
            $table->string('raid_contact');
            $table->string('raid_site_web')->nullable();
            $table->string('raid_lieu')->nullable();
            $table->string('raid_image')->nullable();
            $table->date('date_fin_inscription');
            $table->date('date_debut_inscription');
            $table->integer('nombre_de_courses');
            $table->timestamps();

            $table->foreign('USE_ID')->references('USE_ID')->on('sae_users')->onDelete('cascade');
            $table->foreign('CLU_ID')->references('CLU_ID')->on('sae_clubs')->onDelete('cascade');

            $table->engine = 'InnoDB';
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sae_raids');
    }
};
