<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Inertia\Inertia;
use Illuminate\Support\Facades\Request as RequestFacade;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Models\Visita;

class AppServiceProvider extends ServiceProvider
{
    /**
     * The path to the "home" route for your application.
     *
     * This is used by Laravel authentication to redirect users after login.
     *
     * @var string
     */
    public const HOME = '/';

    /**
     * Register any application services.
     */
    public function register(): void
    {
        Model::unguard();
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Compartir el contador de visitas de la página actual con Inertia
        Inertia::share('visitasPaginaActual', function () {
            $route = RequestFacade::route() ? RequestFacade::route()->getName() : RequestFacade::path();

            // Si no hay nombre de ruta, usar el path
            if (!$route || $route === '/') {
                $route = RequestFacade::path() ?: 'dashboard';
            }

            // Log para debug (solo en desarrollo)
            if (config('app.debug')) {
                Log::info("Obteniendo visitas para ruta", [
                    'route' => $route,
                    'path' => RequestFacade::path()
                ]);
            }

            // Obtener el total de visitas para esta ruta específica
            $count = Visita::where('route', $route)->sum('cantidad');

            if (config('app.debug')) {
                Log::info("Visitas encontradas", [
                    'route' => $route,
                    'count' => $count
                ]);
            }

            return $count;
        });

        $this->bootRoute();
    }

    public function bootRoute(): void
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });

    }
}
