<?php

namespace App\Http\Controllers;

use App\Models\SolicitudTramite;
use App\Models\Tramite;
use App\Models\User;
use App\Models\WorkflowTransicion;
use App\Models\WorkflowEjecucion;
use App\Services\WorkflowService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class SolicitudTramiteController extends Controller
{
    /**
     * Muestra la lista de solicitudes asignadas a la unidad del usuario usando Inertia
     */
    public function index(Request $request)
    {

        $search = $request->input('search');

        $rolIds = Auth::user()->roles->pluck('id');
/*
        if ($rolIds->isEmpty()) {
            abort(403, 'No tienes ningún rol asignado.');
        }
 */

        $solicitudes = SolicitudTramite::with(['usuario', 'tramite'])
            ->pendienteParaRol($rolIds) // Debes definir este scope
            ->search($search)
            ->orderBy('created_at', 'desc')
            ->paginate(10)
            ->appends(['search' => $search]);


        return Inertia::render('Solicitudes/Index', [
            'filters'     => $request->only('search'),
            'solicitudes' => $solicitudes->through(fn($s) => [
                'id'            => $s->id,
                'tramite'       => $s->tramite->nombre,
                'estado_actual' => $s->pasoActual->workflowPaso->nombre,
                'usuario' => $s->usuario ? [
                    'id' => $s->usuario->id,
                    'name' => $s->usuario->first_name,
                ] : null,
                'created_at'    => $s->created_at->format('Y-m-d H:i'),
            ]),
        ]);
    }


    // store
    public function store(Request $request)
{
    $request->validate([
        'tramite_id' => 'required|integer',
        'usuario_id' => 'required|integer',
        'estado_actual' => 'required|string',
        'documentos.*' => 'file|mimes:jpg,jpeg,png,pdf|max:2048',
    ]);

    $solicitud = SolicitudTramite::create($request->only(['tramite_id', 'usuario_id', 'estado_actual']));

    if ($request->hasFile('documentos')) {
        foreach ($request->file('documentos') as $file) {
            $path = $file->store('documentos', 'public');

            $solicitud->documentos()->create([
                'nombre' => $file->getClientOriginalName(),
                'path' => $path,
            ]);
        }
    }

    return redirect()->route('solicitudes.index')->with('success', 'Solicitud creada con éxito');
}


    /**
     * Muestra el detalle de una solicitud y las acciones disponibles
     */
    public function show(SolicitudTramite $solicitud_tramite, WorkflowService $workflow)
    {
        $solicitud_tramite->load(['usuario', 'pasoActual.workflowPaso.transiciones', 'workflowEjecuciones.usuario', 'tramite']);
        $acciones = $workflow->accionesDisponibles($solicitud_tramite);


        $tramite =  [
            'id' => $solicitud_tramite->id,
            'codigo' => $solicitud_tramite->codigo,
            'titulo' => $solicitud_tramite->titulo,
            'estado_actual' => optional($solicitud_tramite->pasoActual?->workflowPaso)->nombre,
            'descripcion' => $solicitud_tramite->descripcion,
            'created_at' => $solicitud_tramite->created_at->toDateTimeString(),
            'usuario' => $solicitud_tramite->usuario ? [
                'id' => $solicitud_tramite->usuario->id,
                'name' => $solicitud_tramite->usuario->first_name,
            ] : null,
            'historial' => $solicitud_tramite->workflowEjecuciones->map(function ($ejecucion) {
                return [
                    'id' => $ejecucion->id,
                    'paso' => optional($ejecucion->workflowPaso)->nombre,
                    'usuario' => optional($ejecucion->usuario)->name,
                    'estado' => $ejecucion->estado,
                    'fecha' => $ejecucion->created_at->toDateTimeString(),
                    'fecha_inicio' => $ejecucion->fecha_inicio,
                    'fecha_fin' => $ejecucion->fecha_fin,
                    'observacion' => $ejecucion->observacion,
                ];
            }),
            'acciones' => $acciones,
        ];



        return Inertia::render('Solicitudes/Show', [
            'tramite' => $tramite,
        ]);
    }


    /**
     * Ejecuta una acción (transición) sobre la solicitud
     */
    public function execute(Request $request, SolicitudTramite $solicitud)
    {
        $request->validate([
            'transicion_id' => 'required|exists:workflow_transiciones,id',
            'comentario'    => 'nullable|string|max:1000',
        ]);

        $transicion = WorkflowTransicion::findOrFail($request->transicion_id);

        // Verificar que la transición corresponde al paso actual
        if ($transicion->paso_origen_id !== $solicitud->pasoActual->id) {
            abort(403, 'Acción no permitida en este paso');
        }

        // Actualizar estado en solicitudes_tramite
        $nuevoPaso = $transicion->pasoDestino;
        $solicitud->estado_actual = $nuevoPaso->nombre;
        $solicitud->save();

        // Crear registro en workflow_ejecuciones
        WorkflowEjecucion::create([
            'solicitud_id' => $solicitud->id,
            'paso_id'      => $nuevoPaso->id,
            'usuario_id'   => Auth::id(),
            'estado'       => 'completado',
            'fecha_inicio' => now(),
            'fecha_fin'    => now(),
        ]);

        return redirect()
            ->route('solicitudes.index')
            ->with('success', 'Acción ejecutada correctamente.');
    }

    public function ejecutarAccion(Request $request, SolicitudTramite $solicitud_tramite)
    {
        $accion = $request->input('accion');

        $workflowService = new WorkflowService();

        try {

            $workflowService->ejecutarTransicion($solicitud_tramite, $accion, Auth::user());

            return redirect()->back()->with('success', 'Acción ejecutada correctamente.');

        } catch (\Exception $e) {
            // Opcional: loguear el error para debug
            //\Log::error('Error al ejecutar acción en workflow: ' . $e->getMessage());

            return redirect()->back()->with('error', 'No se pudo ejecutar la acción.');

        }
    }


    public function create() {
        // Obtenemos solo los campos necesarios para el select: id, nombre, y costo
        $tramites = Tramite::select('id', 'nombre', 'costo')
                            ->orderBy('nombre')
                            ->get();
        // Podrías pasar solo el usuario autenticado o todos los usuarios según tu lógica de negocio
        // Por ahora mantenemos la carga de todos los usuarios
        $usuarios = User::select('id', 'first_name', 'last_name')->get();

        return Inertia::render('Solicitudes/Create',[
                'tramites'=> $tramites,
                'usuarios' => $usuarios // Aunque probablemente solo necesites el id del usuario autenticado
            ]);
    }

}
