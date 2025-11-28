<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Tramite;
use App\Models\SolicitudTramite;
use App\Models\Unidad;
use App\Models\Responsable;
use App\Models\Role;
use App\Models\Workflow;
use App\Models\Documento;

class TestSearch extends Command
{
    protected $signature = 'test:search {query}';
    protected $description = 'Test the search functionality';

    public function handle()
    {
        $query = $this->argument('query');
        $this->info("Testing search for: '{$query}'");

        // Test Users
        $this->info("\n=== Testing Users ===");
        try {
            $users = User::where(function($q) use ($query) {
                $q->where('first_name', 'ilike', "%{$query}%")
                  ->orWhere('last_name', 'ilike', "%{$query}%")
                  ->orWhereRaw("concat(first_name, ' ', last_name) ilike ?", ["%{$query}%"])
                  ->orWhere('email', 'ilike', "%{$query}%");
            })->limit(3)->get();
            
            $this->info("Found {$users->count()} users");
            foreach ($users as $user) {
                $this->line("- {$user->first_name} {$user->last_name} ({$user->email})");
            }
        } catch (\Exception $e) {
            $this->error("Error with Users: " . $e->getMessage());
        }

        // Test Tramites
        $this->info("\n=== Testing Tramites ===");
        try {
            $tramites = Tramite::where('nombre', 'ilike', "%{$query}%")->limit(3)->get();
            $this->info("Found {$tramites->count()} tramites");
            foreach ($tramites as $tramite) {
                $this->line("- {$tramite->nombre}");
            }
        } catch (\Exception $e) {
            $this->error("Error with Tramites: " . $e->getMessage());
        }

        // Test Unidades
        $this->info("\n=== Testing Unidades ===");
        try {
            $unidades = Unidad::where('name', 'ilike', "%{$query}%")->limit(3)->get();
            $this->info("Found {$unidades->count()} unidades");
            foreach ($unidades as $unidad) {
                $this->line("- {$unidad->name}");
            }
        } catch (\Exception $e) {
            $this->error("Error with Unidades: " . $e->getMessage());
        }

        // Test Responsables
        $this->info("\n=== Testing Responsables ===");
        try {
            $responsables = Responsable::where(function($q) use ($query) {
                $q->where('first_name', 'ilike', "%{$query}%")
                  ->orWhere('last_name', 'ilike', "%{$query}%")
                  ->orWhereRaw("concat(first_name, ' ', last_name) ilike ?", ["%{$query}%"])
                  ->orWhere('email', 'ilike', "%{$query}%")
                  ->orWhere('city', 'ilike', "%{$query}%");
            })->limit(3)->get();
            
            $this->info("Found {$responsables->count()} responsables");
            foreach ($responsables as $responsable) {
                $this->line("- {$responsable->name}");
            }
        } catch (\Exception $e) {
            $this->error("Error with Responsables: " . $e->getMessage());
        }

        // Test Roles
        $this->info("\n=== Testing Roles ===");
        try {
            $roles = Role::where('name', 'ilike', "%{$query}%")->limit(3)->get();
            $this->info("Found {$roles->count()} roles");
            foreach ($roles as $role) {
                $this->line("- {$role->name}");
            }
        } catch (\Exception $e) {
            $this->error("Error with Roles: " . $e->getMessage());
        }

        // Test Workflows
        $this->info("\n=== Testing Workflows ===");
        try {
            $workflows = Workflow::where(function($q) use ($query) {
                $q->where('nombre', 'ilike', "%{$query}%")
                  ->orWhere('descripcion', 'ilike', "%{$query}%");
            })->limit(3)->get();
            
            $this->info("Found {$workflows->count()} workflows");
            foreach ($workflows as $workflow) {
                $this->line("- {$workflow->nombre}");
            }
        } catch (\Exception $e) {
            $this->error("Error with Workflows: " . $e->getMessage());
        }

        // Test Documentos
        $this->info("\n=== Testing Documentos ===");
        try {
            $documentos = Documento::where('nombre', 'ilike', "%{$query}%")->limit(3)->get();
            $this->info("Found {$documentos->count()} documentos");
            foreach ($documentos as $documento) {
                $this->line("- {$documento->nombre}");
            }
        } catch (\Exception $e) {
            $this->error("Error with Documentos: " . $e->getMessage());
        }

        $this->info("\nSearch test completed!");
    }
} 