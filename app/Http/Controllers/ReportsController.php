<?php

namespace App\Http\Controllers;


use Carbon\Carbon;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

use Barryvdh\DomPDF\Facade\Pdf as PDF;

class ReportsController extends Controller
{

    //cantidad de solicitudes por tramite
    public function index(): Response
    {
        $reporte = \App\Models\SolicitudTramite::with('tramite')
            ->get()
            ->groupBy(fn($s) => $s->tramite->nombre)
            ->map->count();


        return Inertia::render('Reports/ReportePorTramite', [
            'resumenPorTramite' => $reporte,
            'totalSolicitudes' => collect($reporte)->sum(),
            'solicitudes' => [],
            'filters' => [],
            'menu' => [
                ['title' => 'Reporte por tramites', 'link' => '/reportes/por_tramites'],
                ['title' => 'Reporte por Estado', 'link' => '/reportes/por_estados'],
            ],
        ]);
    }


    //solicitud de tramite por estado

    public function por_estados(Request $request)
    {
        // Filtros desde request
        $fechaDesde = $request->input('fecha_desde');
        $fechaHasta = $request->input('fecha_hasta');
        $estadoFiltro = $request->input('estado');
        $encargadoId = $request->input('encargado_id');
        $tramiteId = $request->input('tramite_id');

        // Consulta principal con filtros
        $tramites = \App\Models\Tramite::with(['solicitudes' => function ($q) use ($fechaDesde, $fechaHasta, $estadoFiltro, $encargadoId, $tramiteId) {
            $q->with(['workflowEjecuciones' => fn($q) => $q->latest()]);

            if ($fechaDesde) {
                $q->whereDate('created_at', '>=', $fechaDesde);
            }
            if ($fechaHasta) {
                $q->whereDate('created_at', '<=', $fechaHasta);
            }
            if ($tramiteId) {
                $q->where('tramite_id', $tramiteId);
            }
            if ($encargadoId) {
                $q->where('usuario_id', $encargadoId);
            }
            // Nota: filtro por estado se aplicará más abajo
        }])->get();

        // Armamos reporte agrupando por último estado de la ejecución y aplicando filtro de estado si aplica
        $reporte = $tramites->mapWithKeys(function ($tramite) use ($estadoFiltro) {
            // Obtener solicitudes que cumplan con filtro de estado en última ejecución
            $solicitudesFiltradas = $tramite->solicitudes->filter(function ($s) use ($estadoFiltro) {
                $ultimoEstado = optional($s->workflowEjecuciones->first())->estado;
                if ($estadoFiltro) {
                    return $ultimoEstado === $estadoFiltro;
                }
                return true;
            });

            // Agrupar por estado de la última ejecución y contar
            $estados = $solicitudesFiltradas->groupBy(fn($s) => optional($s->workflowEjecuciones->first())->estado)
                ->map->count()
                ->toArray();

            return [$tramite->nombre => $estados];
        })->toArray();

        // Pasamos filtros actuales para mantener en el formulario
        $filters = $request->only(['fecha_desde', 'fecha_hasta', 'estado', 'encargado_id', 'tramite_id']);

        // ✅ Exportar PDF si se pidió
        if ($request->has('export') && $request->input('export') === 'pdf') {
            $pdf = PDF::loadView('reportes.tramites_por_estado', [
                'reporte' => $reporte,
                'filters' => $filters,
                'fechaActual' => now()->format('d/m/Y H:i')
            ]);
            return $pdf->download('reporte_por_estados_' . now()->format('Ymd_His') . '.pdf');
        }

        return Inertia::render('Reports/TramitesPorEstado', [
            'reporte' => $reporte,
            'filters' => $filters,
            'menu' => [
                ['title' => 'Reporte por tramites', 'link' => '/reportes/por_tramites'],
                ['title' => 'Reporte por Estado', 'link' => '/reportes/por_estados'],
            ],
        ]);
    }


    public function porTramites(Request $request)
    {
        // Obtener filtros del request
        $fechaDesde = $request->input('fecha_desde'); // formato 'YYYY-MM-DD'
        $fechaHasta = $request->input('fecha_hasta'); // formato 'YYYY-MM-DD'
        $estado     = $request->input('estado');      // ejemplo: 'pendiente', 'aprobado'
        $encargadoId = $request->input('encargado_id'); // id del usuario encargado
        $tramiteId  = $request->input('tramite_id'); // id del trámite

        // Query base con relaciones necesarias
        $query = \App\Models\SolicitudTramite::with(['tramite', 'usuario']);

        // Aplicar filtros
        if ($fechaDesde) {
            $query->whereDate('created_at', '>=', $fechaDesde);
        }
        if ($fechaHasta) {
            $query->whereDate('created_at', '<=', $fechaHasta);
        }
        if ($estado) {
            $query->where('estado_actual', $estado);
        }
        if ($encargadoId) {
            $query->whereHas('usuario', function ($q) use ($encargadoId) {
                $q->where('id', $encargadoId);
            });
        }
        if ($tramiteId) {
            $query->where('tramite_id', $tramiteId);
        }

        $solicitudes = $query->orderBy('created_at', 'desc')->get();

        // Datos agregados para resumen
        $totalSolicitudes = $solicitudes->count();

        // Agrupar por trámite
        $resumenPorTramite = $solicitudes->groupBy(fn($s) => $s->tramite->nombre)
            ->map(fn($items) => $items->count());

        // Ver si quieren exportar PDF
        if ($request->has('export') && $request->input('export') == 'pdf') {
           $pdf = PDF::loadView('reportes.solicitudes_por_tramite', [
                'solicitudes' => $solicitudes,
                'resumenPorTramite' => $resumenPorTramite,
                'totalSolicitudes' => $totalSolicitudes,
                'fechaDesde' => $fechaDesde,
                'fechaHasta' => $fechaHasta,
                'estado' => $estado,
                'encargadoId' => $encargadoId,
                'tramiteId' => $tramiteId,
                'fechaActual' => now()->format('d/m/Y H:i')
            ]);
            return $pdf->download('reporte_solicitudes_' . now()->format('Ymd_His') . '.pdf');
        }


        return Inertia::render('Reports/ReportePorTramite', [
            'solicitudes' => $solicitudes,
            'resumenPorTramite' => $resumenPorTramite,
            'totalSolicitudes' => $totalSolicitudes,
            'filters' => $request->only(['fecha_desde', 'fecha_hasta', 'estado', 'encargado_id', 'tramite_id']),
            'menu' => [
                ['title' => 'Reporte por tramites', 'link' => '/reportes/por_tramites'],
                ['title' => 'Reporte por Estado', 'link' => '/reportes/por_estados'],
            ],
        ]);
    }
}
