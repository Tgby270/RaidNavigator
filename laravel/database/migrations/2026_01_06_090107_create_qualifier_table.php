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
        Schema::create('sae_qualifier', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->foreignId('ROLE_ID')->references('ROLE_ID')->on('sae_role')->cascadeOnDelete();
            $table->foreignId('USE_ID')->references('USE_ID')->on('sae_users')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sae_qualifier');
    }
};
