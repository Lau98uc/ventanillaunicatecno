<?php

namespace App\Console\Commands;

use App\Models\Visita;
use Illuminate\Console\Command;

class ClearVisitData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'visits:clear';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Limpiar todos los datos de visitas';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if ($this->confirm('¿Estás seguro de que quieres eliminar todos los datos de visitas?')) {
            $count = Visita::count();
            Visita::truncate();
            
            $this->info("Se eliminaron {$count} registros de visitas.");
            $this->info('Los datos de visitas han sido limpiados exitosamente.');
        } else {
            $this->info('Operación cancelada.');
        }
    }
} 