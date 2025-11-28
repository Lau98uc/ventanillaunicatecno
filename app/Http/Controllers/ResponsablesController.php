<?php

namespace App\Http\Controllers;

use App\Models\Responsable;
use App\Models\Unidad;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class ResponsablesController extends Controller
{
    public function index(): Response
    {
        $menu = [
            ['title' => 'Unidades', 'link' => '/unidades'],
            ['title' => 'Responsables', 'link' => '/responsables'],
        ];

        return Inertia::render('Responsables/Index', [
            'filters' => Request::all('search', 'trashed'),
            'responsables' => Responsable::with('unidad')
                ->orderByName()
                ->filter(Request::only('search', 'trashed'))
                ->paginate(10)
                ->withQueryString()
                ->through(fn($responsable) => [
                    'id' => $responsable->id,
                    'name' => $responsable->name,
                    'phone' => $responsable->phone,
                    'city' => $responsable->city,
                    'deleted_at' => $responsable->deleted_at,
                    'unidad' => $responsable->unidad?->only('name'),
                ]),
            'menu' => $menu
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Responsables/Create', [
            'unidades' => Unidad::orderBy('name')->get()->map->only('id', 'name'),
        ]);
    }

    public function store(): RedirectResponse
    {
        Responsable::create(
            Request::validate([
                'first_name' => ['required', 'max:50'],
                'last_name' => ['required', 'max:50'],
                'unidad_id' => ['nullable', Rule::exists('unidades', 'id')],
                'email' => ['nullable', 'max:50', 'email'],
                'phone' => ['nullable', 'max:50'],
                'address' => ['nullable', 'max:150'],
                'city' => ['nullable', 'max:50'],
                'region' => ['nullable', 'max:50'],
                'country' => ['nullable', 'max:2'],
            ])
        );

        return Redirect::route('responsables.index')->with('success', 'Responsable creado.');
    }

    public function edit(Responsable $responsable): Response
    {
        return Inertia::render('Responsables/Edit', [
            'responsable' => [
                'id' => $responsable->id,
                'first_name' => $responsable->first_name,
                'last_name' => $responsable->last_name,
                'unidad_id' => $responsable->unidad_id,
                'email' => $responsable->email,
                'phone' => $responsable->phone,
                'address' => $responsable->address,
                'city' => $responsable->city,
                'region' => $responsable->region,
                'country' => $responsable->country,
                'deleted_at' => $responsable->deleted_at,
            ],
            'unidades' => Unidad::orderBy('name')->get()->map->only('id', 'name'),
        ]);
    }

    public function update(Responsable $responsable): RedirectResponse
    {
        $responsable->update(
            Request::validate([
                'first_name' => ['required', 'max:50'],
                'last_name' => ['required', 'max:50'],
                'unidad_id' => ['nullable', Rule::exists('unidades', 'id')],
                'email' => ['nullable', 'max:50', 'email'],
                'phone' => ['nullable', 'max:50'],
                'address' => ['nullable', 'max:150'],
                'city' => ['nullable', 'max:50'],
                'region' => ['nullable', 'max:50'],
                'country' => ['nullable', 'max:2'],
            ])
        );

        return Redirect::back()->with('success', 'Responsable actualizado.');
    }

    public function destroy(Responsable $responsable): RedirectResponse
    {
        $responsable->delete();

        return Redirect::back()->with('success', 'Responsable eliminado.');
    }

    public function restore(Responsable $responsable): RedirectResponse
    {
        $responsable->restore();

        return Redirect::back()->with('success', 'Responsable restaurado.');
    }
}
