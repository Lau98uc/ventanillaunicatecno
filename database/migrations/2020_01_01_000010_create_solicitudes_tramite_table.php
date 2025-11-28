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
        Schema::create('solicitudes_tramite', function (Blueprint $table) {
            $table->id();
            $table->string('nombre')->nullable(); // Título de la solicitud
            $table->foreignId('tramite_id')->constrained('tramites');
            $table->foreignId('usuario_id')->constrained('users');
            $table->string('estado_actual')->nullable(); // opcional para optimizar
            $table->timestamps();
            $table->softDeletes(); // Soft delete support
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('solicitudes_tramite');
    }
};
