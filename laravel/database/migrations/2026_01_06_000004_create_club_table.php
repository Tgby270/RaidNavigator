<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sae_clubs', function (Blueprint $table) {
            $table->id('CLU_ID'); // BIGINT UNSIGNED AUTO_INCREMENT
            $table->unsignedBigInteger('USE_ID'); // FK vers users
            $table->string('CLU_NOM');
            $table->string('CLU_ADRESSE');
            $table->string('CLU_VILLE');
            $table->string('CLU_CODE_POSTAL');
            $table->timestamps();

            $table->foreign('USE_ID')->references('USE_ID')->on('sae_users')->onDelete('cascade');
            $table->engine = 'InnoDB';
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sae_clubs');
    }
};
