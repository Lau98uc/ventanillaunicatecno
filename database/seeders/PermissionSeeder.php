<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PermissionSeeder extends Seeder
{
    public function run()
    {
        $now = now();
        $guard = 'web';

        // Módulos principales
        $modulos = [
            'unidades'     => ['label' => 'Unidades',     'icon' => 'fas fa-building'],
            'responsables' => ['label' => 'Responsables', 'icon' => 'fas fa-user-tie'],
            'tramites'     => ['label' => 'Trámites',     'icon' => 'fas fa-route'],
            'solicitudes'  => ['label' => 'Solicitudes',  'icon' => 'fas fa-file-alt'],
            'usuarios'     => ['label' => 'Usuarios',     'icon' => 'fas fa-users'],
            'reports'     => ['label' => 'Reportes',     'icon' => 'fas fa-chart-bar'],
            'seguridad'    => ['label' => 'Seguridad',    'icon' => 'fas fa-shield-alt'],
        ];

        $moduloIds = [];

        // 1. Insertar módulos
        foreach ($modulos as $key => $data) {
            $id = DB::table('permissions')->insertGetId([
                'name'       => $key,
                'guard_name' => $guard,
                'label'      => $data['label'],
                'slug'       => $key,
                'type'       => 'modulo',
                'icon'       => $data['icon'],
                'visible'    => true,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
            $moduloIds[$key] = $id;
        }

        // 2. Modelos asociados a cada módulo
        $modelos = [
            'unidades'     => ['unidades'],
            'responsables' => ['responsable'],
            'tramites'     => ['tramites', 'workflow'],
            'solicitudes'  => ['solicitudes'],
            'usuarios'     => ['usuarios', 'roles'],
            'reports'     => ['reporte_por_tramites', 'reporte_por_estado'],
        ];

        $acciones = ['ver', 'crear', 'editar', 'eliminar'];

        foreach ($modelos as $modulo => $modelosArr) {
            foreach ($modelosArr as $modelo) {
                // 2.1 Insertar modelo
                $modeloId = DB::table('permissions')->insertGetId([
                    'name'       => $modelo,
                    'guard_name' => $guard,
                    'label'      => ucfirst(str_replace('_', ' ', $modelo)),
                    'slug'       => $modelo,
                    'type'       => 'modelo',
                    'parent_id'  => $moduloIds[$modulo],
                    'visible'    => false,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);

                // 2.2 Insertar acciones para el modelo
                foreach ($acciones as $accion) {
                    DB::table('permissions')->insert([
                        'name'       => $accion . '_' . $modelo,
                        'guard_name' => $guard,
                        'label'      => ucfirst($accion),
                        'slug'       => $accion . '-' . $modelo,
                        'type'       => 'accion',
                        'parent_id'  => $modeloId,
                        'visible'    => false,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ]);
                }
            }
        }
    }
}
