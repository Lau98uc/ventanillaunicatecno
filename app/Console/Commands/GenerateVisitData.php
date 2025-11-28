<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\Visita;
use Illuminate\Console\Command;

class GenerateVisitData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'visits:generate {--days=30}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generar datos de prueba para el contador de visitas';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $days = $this->option('days');
        $users = User::all();
        $routes = [
            'dashboard',
            'users.index',
            'tramites.index',
            'solicitudes.index',
            'reports.index',
            'notifications.index',
        ];

        $this->info("Generando datos de visitas para los últimos {$days} días...");

        $bar = $this->output->createProgressBar($days);
        $bar->start();

        for ($i = 0; $i < $days; $i++) {
            $date = now()->subDays($i);
            
            // Generar entre 10 y 50 visitas por día
            $visitsPerDay = rand(10, 50);
            
            for ($j = 0; $j < $visitsPerDay; $j++) {
                $user = $users->random();
                $route = $routes[array_rand($routes)];
                $visits = rand(1, 5); // Entre 1 y 5 visitas por usuario/ruta
                
                Visita::create([
                    'route' => $route,
                    'cantidad' => $visits,
                    'usuario_id' => $user->id,
                    'created_at' => $date,
                    'updated_at' => $date,
                ]);
            }
            
            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info('¡Datos de visitas generados exitosamente!');
    }
} 