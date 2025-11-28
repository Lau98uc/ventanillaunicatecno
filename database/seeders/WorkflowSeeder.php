<?php

namespace Database\Seeders;

use App\Models\Workflow;
use App\Models\WorkflowPaso;
use App\Models\WorkflowTransicion;
use Illuminate\Database\Seeder;

class WorkflowSeeder extends Seeder
{
    public function run()
    {
        // Workflow 1: Aprobación de Documentos
        $workflow1 = Workflow::create([
            'nombre' => 'Aprobación de Documentos',
            'descripcion' => 'Workflow para la aprobación de documentos administrativos'
        ]);

        // Pasos para el workflow 1
        $paso1_1 = WorkflowPaso::create([
            'workflow_id' => $workflow1->id,
            'nombre' => 'Recepción de Documento',
            'orden' => 1
        ]);

        $paso1_2 = WorkflowPaso::create([
            'workflow_id' => $workflow1->id,
            'nombre' => 'Revisión Técnica',
            'orden' => 2
        ]);

        $paso1_3 = WorkflowPaso::create([
            'workflow_id' => $workflow1->id,
            'nombre' => 'Aprobación Final',
            'orden' => 3
        ]);

        // Transiciones para el workflow 1
        WorkflowTransicion::create([
            'paso_origen_id' => $paso1_1->id,
            'paso_destino_id' => $paso1_2->id,
            'accion' => 'Enviar a Revisión'
        ]);

        WorkflowTransicion::create([
            'paso_origen_id' => $paso1_2->id,
            'paso_destino_id' => $paso1_3->id,
            'accion' => 'Aprobar Técnicamente'
        ]);

        // Workflow 2: Solicitud de Permisos
        $workflow2 = Workflow::create([
            'nombre' => 'Solicitud de Permisos',
            'descripcion' => 'Workflow para el procesamiento de solicitudes de permisos'
        ]);

        // Pasos para el workflow 2
        $paso2_1 = WorkflowPaso::create([
            'workflow_id' => $workflow2->id,
            'nombre' => 'Presentación de Solicitud',
            'orden' => 1
        ]);

        $paso2_2 = WorkflowPaso::create([
            'workflow_id' => $workflow2->id,
            'nombre' => 'Evaluación de Requisitos',
            'orden' => 2
        ]);

        $paso2_3 = WorkflowPaso::create([
            'workflow_id' => $workflow2->id,
            'nombre' => 'Emisión de Permiso',
            'orden' => 3
        ]);

        // Transiciones para el workflow 2
        WorkflowTransicion::create([
            'paso_origen_id' => $paso2_1->id,
            'paso_destino_id' => $paso2_2->id,
            'accion' => 'Evaluar Requisitos'
        ]);

        WorkflowTransicion::create([
            'paso_origen_id' => $paso2_2->id,
            'paso_destino_id' => $paso2_3->id,
            'accion' => 'Aprobar Solicitud'
        ]);

        // Workflow 3: Trámite de Títulos
        $workflow3 = Workflow::create([
            'nombre' => 'Trámite de Títulos',
            'descripcion' => 'Workflow para el procesamiento de títulos académicos'
        ]);

        // Pasos para el workflow 3
        $paso3_1 = WorkflowPaso::create([
            'workflow_id' => $workflow3->id,
            'nombre' => 'Solicitud de Título',
            'orden' => 1
        ]);

        $paso3_2 = WorkflowPaso::create([
            'workflow_id' => $workflow3->id,
            'nombre' => 'Verificación de Créditos',
            'orden' => 2
        ]);

        $paso3_3 = WorkflowPaso::create([
            'workflow_id' => $workflow3->id,
            'nombre' => 'Impresión de Título',
            'orden' => 3
        ]);

        // Transiciones para el workflow 3
        WorkflowTransicion::create([
            'paso_origen_id' => $paso3_1->id,
            'paso_destino_id' => $paso3_2->id,
            'accion' => 'Verificar Créditos'
        ]);

        WorkflowTransicion::create([
            'paso_origen_id' => $paso3_2->id,
            'paso_destino_id' => $paso3_3->id,
            'accion' => 'Aprobar Impresión'
        ]);
    }
} 