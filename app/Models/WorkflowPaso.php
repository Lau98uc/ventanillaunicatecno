<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WorkflowPaso extends Model
{
    protected $fillable = ['workflow_id', 'nombre', 'orden', 'unidad_id'];

    public function workflow()
    {
        return $this->belongsTo(Workflow::class);
    }

    public function unidad()
    {
        return $this->belongsTo(Unidad::class);
    }

    public function transiciones()
    {
        return $this->hasMany(WorkflowTransicion::class, 'paso_origen_id');
    }

    public function transicionesOrigen()
    {
        return $this->hasMany(WorkflowTransicion::class, 'paso_origen_id');
    }

    public function transicionesDestino()
    {
        return $this->hasMany(WorkflowTransicion::class, 'paso_destino_id');
    }

    public function ejecuciones()
    {
        return $this->hasMany(WorkflowEjecucion::class, 'paso_id');
    }
}
