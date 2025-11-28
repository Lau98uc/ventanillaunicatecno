<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TransaccionQR extends Model
{
    protected $table = 'transacciones_qr';

    protected $fillable = [
        'payment_number',
        'transaction_id',
        'monto',
        'estado',
        'client_name',
        'document_id',
        'email',
        'tramite_id',
        'usuario_id',
        'qr_image',
        'qr_url',
        'expires_at',
        'order_detail',
        'response_data',
        'pagado_at',
    ];

    protected $casts = [
        'monto' => 'decimal:2',
        'order_detail' => 'array',
        'response_data' => 'array',
        'expires_at' => 'datetime',
        'pagado_at' => 'datetime',
    ];

    // Estados posibles
    const ESTADO_PENDIENTE = 'pendiente';
    const ESTADO_PAGADO = 'pagado';
    const ESTADO_EXPIRADO = 'expirado';
    const ESTADO_CANCELADO = 'cancelado';

    // Relaciones
    public function tramite()
    {
        return $this->belongsTo(\App\Models\Tramite::class);
    }

    public function usuario()
    {
        return $this->belongsTo(\App\Models\User::class);
    }

    // Scopes
    public function scopePendiente($query)
    {
        return $query->where('estado', self::ESTADO_PENDIENTE);
    }

    public function scopePagado($query)
    {
        return $query->where('estado', self::ESTADO_PAGADO);
    }

    // Métodos de utilidad
    public function marcarComoPagado()
    {
        $this->update([
            'estado' => self::ESTADO_PAGADO,
            'pagado_at' => now(),
        ]);
    }

    public function estaExpirado()
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    public function estaPagado()
    {
        return $this->estado === self::ESTADO_PAGADO;
    }

    public function estaPendiente()
    {
        return $this->estado === self::ESTADO_PENDIENTE;
    }
}
