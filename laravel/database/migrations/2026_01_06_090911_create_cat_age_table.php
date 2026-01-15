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
        Schema::create('sae_cat_age', function (Blueprint $table) {
            $table->id('AGE_ID');
            $table->timestamps();
            $table->integer('AGE_MIN');
            $table->integer('AGE_MAX');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sae_cat_age');
    }
};
