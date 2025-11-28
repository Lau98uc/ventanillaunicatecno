<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Workflow extends Model
{
    protected $fillable = ['nombre', 'descripcion'];

    public function tramites()
    {
        return $this->hasMany(Tramite::class);
    }

    public function pasos()
    {
        return $this->hasMany(WorkflowPaso::class);
    }

    public function transiciones()
    {
        return $this->hasManyThrough(
            WorkflowTransicion::class,   // Modelo destino
            WorkflowPaso::class,         // Modelo intermedio
            'workflow_id',               // Clave en pasos
            'paso_origen_id',            // Clave en transiciones
            'id',                        // Clave local en workflow
            'id'                         // Clave local en pasos
        );
    }
}
