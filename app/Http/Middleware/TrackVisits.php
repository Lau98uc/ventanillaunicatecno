<?php

namespace App\Http\Middleware;

use App\Models\Visita;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class TrackVisits
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Solo rastrear visitas si el usuario está autenticado
        if (Auth::check()) {
            $route = $request->route()->getName() ?? $request->path();
            
            // Si no hay nombre de ruta o es la ruta raíz, usar 'dashboard'
            if (!$route || $route === '/') {
                $route = 'dashboard';
            }
            
            // No rastrear visitas a rutas de API, assets, o rutas que no queremos contar
            if (!str_starts_with($route, 'api.') && 
                !str_starts_with($route, 'sanctum.') &&
                !str_contains($route, 'assets') &&
                !str_contains($route, 'build') &&
                !str_contains($route, 'image') &&
                $request->isMethod('GET')) {
                
                // Log para debug (solo en desarrollo)
                if (config('app.debug')) {
                    Log::info("Registrando visita", [
                        'route' => $route,
                        'user_id' => Auth::id(),
                        'path' => $request->path(),
                        'method' => $request->method()
                    ]);
                }
                
                // Registrar la visita ANTES de procesar la respuesta
                Visita::incrementarVisita($route, Auth::id());
            }
        }

        $response = $next($request);
        return $response;
    }
} 