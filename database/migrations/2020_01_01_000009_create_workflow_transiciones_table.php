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
        Schema::create('workflow_transiciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('paso_origen_id')->constrained('workflow_pasos');
            $table->foreignId('paso_destino_id')->nullable()->constrained('workflow_pasos');
            $table->string('accion'); // nombre de la transición, ej: "aprobar"
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('workflow_transiciones');
    }
};
