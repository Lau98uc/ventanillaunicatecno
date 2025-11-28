<?php

namespace App\Console\Commands;

use App\Models\Visita;
use Illuminate\Console\Command;

class DebugVisits extends Command
{
    protected $signature = 'visits:debug';
    protected $description = 'Debuggear estadísticas de visitas';

    public function handle()
    {
        $this->info('=== DEBUGGING VISITAS ===');
        
        // Verificar datos en la tabla
        $totalRegistros = Visita::count();
        $this->info("Total registros en tabla: {$totalRegistros}");
        
        $totalVisitas = Visita::sum('cantidad');
        $this->info("Total visitas sumadas: {$totalVisitas}");
        
        // Probar método obtenerEstadisticas
        $this->info('--- Probando obtenerEstadisticas() ---');
        $stats = Visita::obtenerEstadisticas();
        $this->info("Total: {$stats['total']}");
        $this->info("Hoy: {$stats['hoy']}");
        $this->info("Esta semana: {$stats['esta_semana']}");
        $this->info("Este mes: {$stats['este_mes']}");
        
        // Probar rutas más visitadas
        $this->info('--- Probando rutasMasVisitadas() ---');
        $rutas = Visita::rutasMasVisitadas(5);
        foreach ($rutas as $ruta) {
            $this->info("Ruta: {$ruta['route']} - Visitas: {$ruta['total_visitas']}");
        }
        
        // Verificar algunos registros
        $this->info('--- Primeros 5 registros ---');
        $registros = Visita::limit(5)->get();
        foreach ($registros as $registro) {
            $this->info("ID: {$registro->id}, Ruta: {$registro->route}, Cantidad: {$registro->cantidad}, Usuario: {$registro->usuario_id}");
        }
    }
} 