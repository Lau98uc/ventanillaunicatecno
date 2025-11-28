<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNotificationsTable extends Migration
{
    public function up()
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();

            // Usuario que recibe la notificación
            $table->foreignId('user_id')->constrained()->onDelete('cascade');

            // Título y mensaje de la notificación
            $table->string('title');
            $table->text('message');

            // Tipo flexible (ej: tramite.asignado, tramite.seguimiento, usuario.bloqueado, etc.)
            $table->string('type')->nullable();

            // Relación polimórfica opcional con cualquier modelo del sistema
            $table->unsignedBigInteger('notifiable_id')->nullable();
            $table->string('notifiable_type')->nullable();
            $table->index(['notifiable_type', 'notifiable_id']); // Índice para consultas

            // Link asociado y/o imagen
            $table->string('link')->nullable();   // Ruta o URL
            $table->string('image')->nullable();  // URL de ícono o imagen

            // Lectura
            $table->boolean('is_read')->default(false);
            $table->timestamp('read_at')->nullable();

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('notifications');
    }
}
