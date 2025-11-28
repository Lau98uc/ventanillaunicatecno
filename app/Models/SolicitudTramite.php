<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SolicitudTramite extends Model
{
    use SoftDeletes;

    protected $table = 'solicitudes_tramite';

    protected $fillable = ['tramite_id', 'usuario_id', 'estado_actual'];

    public function tramite()
    {
        return $this->belongsTo(Tramite::class);
    }

    public function pasoActual()
    {

        $estadoPendiente = $this->hasOne(WorkflowEjecucion::class, 'solicitud_id')
            ->where('estado', 'pendiente') // o el valor correcto según tu lógica
            ->latest('id') // si esperas que la última sea la actual
            ->with('workflowPaso');

        return $estadoPendiente;
    }

    public function usuario()
    {
        return $this->belongsTo(User::class);
    }

    public function workflowEjecuciones()
    {
        return $this->hasMany(WorkflowEjecucion::class, 'solicitud_id');
    }
    public function ejecuciones()
    {
        return $this->hasMany(WorkflowEjecucion::class, 'solicitud_id');
    }

    public function scopePendienteEnUnidad($query, $unidadId)
    {
        return $query->whereHas('workflowEjecuciones', function ($q) use ($unidadId) {
            $q->where('estado', 'pendiente')
                ->whereHas('paso', fn($q2) => $q2->where('unidad_id', $unidadId));
        });
    }

    public function scopePendienteParaRol($query, $rolIds)
    {
        return $query->whereHas('workflowEjecuciones', function ($q) use ($rolIds) {
            $q->where('estado', 'pendiente')
                ->whereHas('workflowPaso', function ($q2) use ($rolIds) {
                    $q2->whereIn('role_id', $rolIds);
                });
        });
    }


    /**
     * Scope: búsqueda por código o nombre de usuario.
     */
    public function scopeSearch($query, $term)
    {
        if (!$term) {
            return $query;
        }

        return $query->where('codigo', 'like', "%{$term}%")
            ->orWhereHas(
                'usuario',
                fn($q) =>
                $q->where('nombre', 'like', "%{$term}%")
            );
    }

    public function documentos()
{
    return $this->hasMany(Documento::class, 'solicitud_id');
}



}
