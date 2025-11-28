<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WorkflowEjecucion extends Model
{

    protected $table = 'workflow_ejecuciones';

    protected $fillable = [
        'solicitud_id',
        'paso_id',
        'usuario_id',
        'estado',
        'fecha_inicio',
        'created_at',
        'fecha_fin'
    ];

    public function solicitud()
    {
        return $this->belongsTo(SolicitudTramite::class, 'solicitud_id');
    }

    public function paso()
    {
        return $this->belongsTo(WorkflowPaso::class, 'paso_id');
    }

    public function workflowPaso()
    {
        return $this->belongsTo(WorkflowPaso::class, 'paso_id', 'id');
    }


    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }
}
