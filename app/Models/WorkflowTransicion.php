<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WorkflowTransicion extends Model
{
    protected $table = 'workflow_transiciones';

    protected $fillable = ['paso_origen_id', 'paso_destino_id', 'accion'];

    public function pasoOrigen()
    {
        return $this->belongsTo(WorkflowPaso::class, 'paso_origen_id');
    }

    public function pasoDestino()
    {
        return $this->belongsTo(WorkflowPaso::class, 'paso_destino_id');
    }
}
