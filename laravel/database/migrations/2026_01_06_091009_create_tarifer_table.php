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
        Schema::create('sae_tarifer', function (Blueprint $table) {
    $table->unsignedBigInteger('RAID_ID');
    $table->unsignedBigInteger('CRS_ID');
    $table->unsignedBigInteger('AGE_ID'); // optionnel, PK locale
    $table->integer('PRIX');
    $table->timestamps();

    // FK composite vers course
    $table->foreign(['RAID_ID', 'CRS_ID'])
          ->references(['RAID_ID', 'CRS_ID'])
          ->on(table: 'sae_course')
          ->onDelete('cascade');

    // FK vers age
    $table->foreign('AGE_ID')
          ->references('AGE_ID')
          ->on(table: 'sae_cat_age')
          ->onDelete('cascade');

    $table->primary(['RAID_ID', 'CRS_ID', 'AGE_ID']); // PK composite locale
    $table->engine = 'InnoDB';
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sae_tarifer');
    }
};