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
        Schema::create('workflow_ejecuciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('solicitud_id')->constrained('solicitudes_tramite');
            $table->foreignId('paso_id')->constrained('workflow_pasos');
            $table->foreignId('usuario_id')->nullable()->constrained('users'); // quién lo realizó
            $table->string('estado'); // pendiente, en_progreso, completado, rechazado...
            $table->timestamp('fecha_inicio')->nullable();
            $table->timestamp('fecha_fin')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('workflow_ejecuciones');
    }
};
