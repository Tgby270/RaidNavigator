<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sae_users', function (Blueprint $table) {
            $table->id('USE_ID'); // BIGINT UNSIGNED AUTO_INCREMENT
            $table->string('USE_NOM');
            $table->string('USE_PRENOM');
            $table->string('USE_MAIL')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('USE_MDP');
            $table->rememberToken();
            $table->date('USE_DATE_NAISSANCE');
            $table->string('USE_NUM_LICENCIE')->nullable();
            $table->string('USE_NUM_PPS')->nullable();
            $table->string('USE_ADRESSE');
            $table->string('USE_TELEPHONE');
            $table->string('USE_CODE_POSTAL');
            $table->string('USE_VILLE');
            $table->timestamps();

            $table->engine = 'InnoDB';
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });

    }

    public function down(): void
    {
        Schema::dropIfExists('sae_users');
    }
};
