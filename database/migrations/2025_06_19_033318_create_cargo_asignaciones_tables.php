<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


return new class extends Migration {

    public function up()
    {
        Schema::create('cargo_asignaciones', function (Blueprint $table) {
            $table->id();

            $table->foreignId('usuario_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('rol_id')->constrained('roles')->onDelete('cascade');
            $table->foreignId('unidad_id')->nullable()->constrained('unidades')->onDelete('set null');
            $table->foreignId('superior_rol_id')->nullable()->constrained('roles')->onDelete('set null');

            $table->date('fecha_inicio');
            $table->date('fecha_fin')->nullable();

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('cargo_asignaciones');
    }
};
