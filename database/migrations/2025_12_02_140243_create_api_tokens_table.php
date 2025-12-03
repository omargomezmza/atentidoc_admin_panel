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
        Schema::create('api_tokens', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            
            // Token de acceso (se usa en cada request)
            $table->text('access_token');
            
            // Token de refresco (para obtener nuevos access_token)
            $table->text('refresh_token')->nullable();
            
            // Cuándo expira el access_token
            $table->timestamp('expires_at')->nullable();
            
            $table->timestamps();
            
            // Índice para búsquedas rápidas
            $table->index('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('api_tokens');
    }
};
