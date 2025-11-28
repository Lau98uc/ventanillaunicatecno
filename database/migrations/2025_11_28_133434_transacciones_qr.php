<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
   public function up()
    {
        Schema::create('transacciones_qr', function (Blueprint $table) {
            $table->id();
            $table->string('payment_number')->unique(); // ID de la transacción de PagoFacil
            $table->string('transaction_id')->nullable(); // ID adicional de PagoFacil
            $table->decimal('monto', 10, 2)->nullable();
            $table->string('estado')->default('pendiente'); // pendiente, pagado, expirado, cancelado

            // Datos del cliente
            $table->string('client_name')->nullable();
            $table->string('document_id')->nullable();
            $table->string('email')->nullable();

            // Datos del trámite
            $table->unsignedBigInteger('tramite_id')->nullable();
            $table->unsignedBigInteger('usuario_id')->nullable();

            // Datos del QR
            $table->text('qr_image')->nullable(); // Base64 del QR
            $table->string('qr_url')->nullable();
            $table->timestamp('expires_at')->nullable();

            // Metadatos
            $table->json('order_detail')->nullable();
            $table->json('response_data')->nullable(); // Guardar toda la respuesta de PagoFacil
            $table->timestamp('pagado_at')->nullable();

            $table->timestamps();

            // Índices
            $table->index('estado');
            $table->index(['estado', 'created_at']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('transacciones_qr');
    }
};
