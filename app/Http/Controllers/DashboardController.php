<?php

namespace App\Http\Controllers;

use App\Models\Visita;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function index(Request $request): Response
    {
        // Obtener estadísticas de visitas (sin incrementar para evitar conflictos)
        $estadisticasVisitas = Visita::obtenerEstadisticas();
        $rutasMasVisitadas = Visita::rutasMasVisitadas(5);

        // Debug: Log de los datos
        Log::info('Dashboard - Estadísticas de visitas:', $estadisticasVisitas);
        Log::info('Dashboard - Rutas más visitadas:', $rutasMasVisitadas);

        // El middleware TrackVisits ya maneja el incremento de visitas
        // No necesitamos incrementar aquí para evitar duplicados

        return Inertia::render('Dashboard/Index', [
            'estadisticasVisitas' => $estadisticasVisitas,
            'rutasMasVisitadas' => $rutasMasVisitadas,
        ]);
    }
}
