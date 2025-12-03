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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            // ID del usuario en la API externa (fuente de verdad)
            $table->string('api_user_id')->unique();
            
            // Datos mínimos necesarios para Laravel Auth
            $table->string('email')->unique();

            // Datos adicionales (opcional)
            $table->string('status')->nullable();
            $table->string('role')->nullable();
            $table->string('avatar_url')->nullable();
            
            // No necesitamos password porque la autenticación es contra la API
            // No necesitamos remember_token porque usamos tokens de API
            
            $table->timestamps();

        });

        /* Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        }); */

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
