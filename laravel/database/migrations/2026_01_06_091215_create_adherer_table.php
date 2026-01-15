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
       Schema::create('sae_adherer', function (Blueprint $table) {
    $table->unsignedBigInteger('CLU_ID'); // FK vers clubs
    $table->unsignedBigInteger('USE_ID'); // FK vers users
    $table->timestamps();

    $table->primary(['CLU_ID', 'USE_ID']); // PK composite

    $table->foreign('CLU_ID')->references('CLU_ID')->on('sae_clubs')->onDelete('cascade');
    $table->foreign('USE_ID')->references('USE_ID')->on('sae_users')->onDelete('cascade');

    $table->engine = 'InnoDB';
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sae_adherer');
    }
};
