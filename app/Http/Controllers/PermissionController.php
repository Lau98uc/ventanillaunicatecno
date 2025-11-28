<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Inertia\Inertia;

class PermissionController extends Controller
{
    public function index()
    {
        $permissions = Permission::all();

        $grouped = $permissions->groupBy(function ($perm) {
            return explode('.', $perm->name)[0];
        });

        $grouped = $grouped->map(function ($perms) {
            return $perms->filter(function ($perm) {
                return $perm->type === 'accion';
            })->values(); // reindex
        })->filter(function ($perms) {
            return $perms->isNotEmpty();
        });

        return Inertia::render('Permissions/Index', [
            'permissions' => $grouped,
            'menu' => [
                ['title' => 'Usuarios', 'link' => '/users'],
                ['title' => 'Roles', 'link' => '/roles'],
                ['title' => 'Permisos', 'link' => '/permissions']
            ],
        ]);
    }



    public function create()
    {
        return Inertia::render('Permissions/Create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:permissions,name',
        ]);

        Permission::create([
            'name' => $request->name,
            'guard_name' => 'web',
        ]);

        return redirect()->route('permissions.index')->with('success', 'Permiso creado correctamente.');
    }

    public function destroy(Permission $permission)
    {
        $permission->delete();

        return redirect()->route('permissions.index')->with('success', 'Permiso eliminado.');
    }
}
