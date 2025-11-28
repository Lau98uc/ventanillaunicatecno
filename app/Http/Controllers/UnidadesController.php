<?php

namespace App\Http\Controllers;

use App\Models\Unidad;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Request;
use Inertia\Inertia;
use Inertia\Response;

class UnidadesController extends Controller
{
    public function index(): Response
    {
        $menu = [
            ['title' => 'Unidades', 'link' => '/unidades'],
            ['title' => 'Responsables', 'link' => '/responsables'],
        ];

        return Inertia::render('Unidades/Index', [
            'filters' => Request::all('search', 'trashed'),
            'unidades' => Unidad::query()
                ->orderBy('name')
                ->filter(Request::only('search', 'trashed'))
                ->paginate(10)
                ->withQueryString()
                ->through(fn($unidad) => [
                    'id' => $unidad->id,
                    'name' => $unidad->name,
                    'phone' => $unidad->phone,
                    'city' => $unidad->city,
                    'deleted_at' => $unidad->deleted_at,
                ]),
            'menu' => $menu
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Unidades/Create');
    }

    public function store(): RedirectResponse
    {
        Unidad::create(
            Request::validate([
                'name' => ['required', 'max:100'],

                'phone' => ['nullable', 'max:50'],
                'address' => ['nullable', 'max:150'],
                'city' => ['nullable', 'max:50'],
                'region' => ['nullable', 'max:50'],
                'country' => ['nullable', 'max:2'],
            ])
        );

        return Redirect::route('unidades')->with('success', 'Unidad creada.');
    }

    public function edit(Unidad $unidad): Response
    {
        return Inertia::render('Unidades/Edit', [
            'unidad' => [
                'id' => $unidad->id,
                'name' => $unidad->name,
                'email' => $unidad->email,
                'phone' => $unidad->phone,
                'address' => $unidad->address,
                'city' => $unidad->city,
                'region' => $unidad->region,
                'country' => $unidad->country,
                'deleted_at' => $unidad->deleted_at,
                'responsables' => $unidad->responsables()->orderByName()->get()->map->only('id', 'name', 'city', 'phone'),
            ],
        ]);
    }

    public function update(Unidad $unidad): RedirectResponse
    {
        $unidad->update(
            Request::validate([
                'name' => ['required', 'max:100'],
                'phone' => ['nullable', 'max:50'],
                'address' => ['nullable', 'max:150'],
                'city' => ['nullable', 'max:50'],
                'region' => ['nullable', 'max:50'],
                'country' => ['nullable', 'max:2'],
            ])
        );

        return Redirect::back()->with('success', 'Unidad actualizada.');
    }

    public function destroy(Unidad $unidad): RedirectResponse
    {
        $unidad->delete();

        return Redirect::back()->with('success', 'Unidad eliminada.');
    }

    public function restore(Unidad $unidad): RedirectResponse
    {
        $unidad->restore();

        return Redirect::back()->with('success', 'Unidad restaurada.');
    }
}
