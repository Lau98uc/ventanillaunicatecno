<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Tramite extends Model
{
    //use SoftDeletes;

    protected $fillable = [
        'nombre',
        'workflow_id',
        'costo',
    ];

    protected $cats = [
        'costo' => 'decimal:2',
    ];

    public function workflow()
    {
        return $this->belongsTo(Workflow::class);
    }

    // Scope para ordenar por nombre
    public function scopeOrderByName($query)
    {
        return $query->orderBy('nombre'); // Asegúrate de que tu tabla tiene una columna 'nombre'
    }

    // Scope para filtrar por búsqueda
    public function scopeFilter($query, array $filters)
    {
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where('nombre', 'like', "%{$search}%");
        }
    }

    public function solicitudes()
    {
        return $this->hasMany(SolicitudTramite::class, 'tramite_id'); // ajusta 'tramite_id' si tu FK tiene otro nombre
    }

        public function esGratuito()
    {
        return $this->costo == 0;
    }

    /**
     * Verificar si requiere pago
     */
    public function requierePago()
    {
        return $this->costo > 0;
    }

    /**
     * Obtener costo formateado
     */
    public function getCostoFormateadoAttribute()
    {
        return number_format($this->costo, 2, '.', ',') . ' Bs';
    }

}
