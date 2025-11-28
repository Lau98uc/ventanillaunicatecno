<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\Permission;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class RoleController extends Controller
{
    public function index(): Response
    {
        $roles = Role::query()
            ->with('permissions')
            ->orderByName()
            ->filter(\Illuminate\Support\Facades\Request::all('search'))
            ->paginate(10)->withPath('/roles')
            ->withQueryString();

        return Inertia::render('Roles/Index', [
            'roles' => $roles,
            'filters' => \Illuminate\Support\Facades\Request::all('search'),
            'menu' => [
                ['title' => 'Usuarios', 'link' => '/users'],
                ['title' => 'Roles', 'link' => '/roles'],
                ['title' => 'Permisos', 'link' => '/permissions']
            ],
        ]);
    }

    public function create()
    {
        // Obtener los permisos jerarquizados: modulo -> menu -> permisos
        $permisos = Permission::with(['children.children'])
            ->where('type', 'modulo') // Solo los padres principales
            ->orderBy('name')
            ->get();

        return Inertia::render('Roles/Create', [
            'permisos' => $permisos,
        ]);

    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:roles,name',
            'permissions' => 'array',
        ]);

        $role = Role::create([
            'name' => $request->name,
            'guard_name' => 'web',
        ]);

        $role->syncPermissions($request->permissions);

        return redirect()->route('roles.index')->with('success', 'Rol creado correctamente.');
    }

    public function edit(Role $role)
    {
        $permisos = Permission::with(['children.children'])
            ->where('type', 'modulo')
            ->orderBy('orden')
            ->get();

        $permisosAsignados = $role->permissions()->pluck('id')->toArray();

        return Inertia::render('Roles/Edit', [
            'role' => $role,
            'permisos' => $permisos,
            'permisosAsignados' => $permisosAsignados,
            'menu' => [
                ['title' => 'Usuarios', 'link' => '/users'],
                ['title' => 'Roles', 'link' => '/roles'],
                ['title' => 'Permisos', 'link' => '/permissions']
            ],
        ]);

    }

    public function update(Request $request, Role $role)
    {
        $request->validate([
            'name' => 'required|unique:roles,name,' . $role->id,
            'permissions' => 'array',
        ]);

        $role->update([
            'name' => $request->name,
        ]);

        $role->syncPermissions($request->permissions);

        return redirect()->route('roles.index')->with('success', 'Rol actualizado.');
    }

    public function destroy(Role $role)
    {
        $role->delete();

        return redirect()->route('roles.index')->with('success', 'Rol eliminado.');
    }
}
