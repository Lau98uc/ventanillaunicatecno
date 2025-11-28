<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB;

class Visita extends Model
{
    use HasFactory;

    protected $fillable = [
        'route',
        'cantidad',
        'usuario_id',
    ];

    protected $casts = [
        'cantidad' => 'integer',
    ];

    /**
     * Relación con el usuario
     */
    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    /**
     * Incrementar el contador de visitas para una ruta específica
     */
    public static function incrementarVisita(string $route, int $usuarioId): void
    {
        // Buscar si ya existe un registro para esta ruta y usuario
        $visita = self::where('route', $route)
            ->where('usuario_id', $usuarioId)
            ->first();

        if ($visita) {
            // Si existe, incrementar la cantidad
            $visita->increment('cantidad');
        } else {
            // Si no existe, crear nuevo registro
            self::create([
                'route' => $route,
                'usuario_id' => $usuarioId,
                'cantidad' => 1,
            ]);
        }
    }

    /**
     * Obtener estadísticas de visitas
     */
    public static function obtenerEstadisticas(): array
    {
        $totalVisitas = self::sum('cantidad');
        $visitasHoy = self::whereDate('created_at', today())->sum('cantidad');
        $visitasEstaSemana = self::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->sum('cantidad');
        $visitasEsteMes = self::whereMonth('created_at', now()->month)->sum('cantidad');

        return [
            'total' => $totalVisitas,
            'hoy' => $visitasHoy,
            'esta_semana' => $visitasEstaSemana,
            'este_mes' => $visitasEsteMes,
        ];
    }

    /**
     * Obtener las rutas más visitadas
     */
    public static function rutasMasVisitadas(int $limit = 5): array
    {
        return self::select('route', DB::raw('SUM(cantidad) as total_visitas'))
            ->groupBy('route')
            ->orderByDesc('total_visitas')
            ->limit($limit)
            ->get()
            ->toArray();
    }
} 