<?php

namespace App\Services;

use App\Models\Role;
use App\Models\SolicitudTramite;
use App\Models\User;
use App\Models\WorkflowEjecucion;
use App\Models\WorkflowPaso;
use App\Models\WorkflowTransicion;
use App\Traits\NotifiesUsers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class WorkflowService
{
    use NotifiesUsers;

    public function ejecutarTransicion(SolicitudTramite $solicitud, string $accion, ?string $observacion = null): void
    {
        // Paso actual pendiente
        $pasoActual = $solicitud->pasoActual;

        if (!$pasoActual) {
            throw ValidationException::withMessages([
                'accion' => 'No hay un paso pendiente para esta solicitud.',
            ]);
        }

        // Transición válida desde este paso
        $transicion = WorkflowTransicion::where('paso_origen_id', $pasoActual->paso_id)
            ->where('accion', $accion)
            ->first();

        if (!$transicion) {
            throw ValidationException::withMessages([
                'accion' => 'La acción indicada no es válida para este paso.',
            ]);
        }

        // Ejecutar transición
        DB::transaction(function () use ($pasoActual, $transicion, $solicitud, $observacion) {
            // 1. Cerrar paso actual
            $pasoActual->update([
                'estado'       => 'completado',
                'fecha_fin'    => now(),
                //'observacion'  => $observacion,
                'usuario_id'   => $pasoActual->usuario_id ?? Auth::id(), // fallback
            ]);

            // 2. Si hay paso siguiente, crear nueva ejecución
            if ($transicion->paso_destino_id) {
                $nuevoPaso = WorkflowEjecucion::create([
                    'solicitud_id' => $solicitud->id,
                    'paso_id'      => $transicion->paso_destino_id,
                    'estado'       => 'pendiente',
                    'fecha_inicio' => now(),
                ]);


                // Obtener el paso relacionado para sacar el role_id
                $pasoDestino = $nuevoPaso->paso; // asumo relación 'paso' en WorkflowEjecucion que apunta a WorkflowPaso

                $role = Role::find($pasoDestino->role_id);

                if ($role) {
                    $usuarios = User::role($role->name)->get();

                    foreach ($usuarios as $usuario) {
                        $this->notifyUser(
                            $usuario->id,
                            'Nuevo trámite asignado',
                            'Tienes un nuevo trámite pendiente para gestionar.',
                            'tramite.asignado',
                            route('solicitudes.show', $solicitud->id), // ✅ esta línea está bien
                            null,
                            $solicitud
                        );
                    }

                }
            } else {
                // Si no hay paso destino, se considera finalizado
                $solicitud->update([
                    'estado_actual' => 'finalizado', // o cualquier otro campo si lo manejas así
                ]);
            }

            // EVENTUAL: lanzar evento o notificación
            // event(new PasoCompletado($solicitud, $transicion));
        });

    }

    /**
     * Retorna las acciones disponibles desde el paso actual
     */
    public function accionesDisponibles(SolicitudTramite $solicitud): array
    {
        $pasoActual = $solicitud->pasoActual;

        if (!$pasoActual || !$pasoActual->workflowPaso) {
            return [];
        }

        $paso = $pasoActual->workflowPaso;
        $roleIdDelPaso = $paso->role_id;

        // Verificar si el usuario tiene ese rol
        $usuario = Auth::user();
        $tieneRol = $usuario->roles->pluck('id')->contains($roleIdDelPaso);

        if (!$tieneRol) {
            return [];
        }

        // Si tiene el rol, devolver las acciones posibles desde ese paso
        return WorkflowTransicion::where('paso_origen_id', $paso->id)
            ->pluck('accion')
            ->toArray();
    }


    /**
     * Opcional: Forzar asignación manual de un paso (útil para pruebas)
     */
    public function asignarPaso(SolicitudTramite $solicitud, WorkflowPaso $paso): void
    {
        WorkflowEjecucion::create([
            'solicitud_id' => $solicitud->id,
            'paso_id'      => $paso->id,
            'estado'       => 'pendiente',
            'fecha_inicio' => now(),
            'usuario_id'   => Auth::id(),
        ]);
    }
}
