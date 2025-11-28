<?php

namespace App\Http\Controllers;

use App\Models\Tramite;
use App\Models\Workflow;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;

class TramiteController extends Controller
{
    public function index(Request $request)
    {
        $tramites = Tramite::with(['workflow'])
            ->orderByName()
            ->filter($request->only('search'))
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('Tramites/Index', [
            'filters' => $request->all('search'),
            'tramites' => $tramites,
        ]);
    }

    public function create()
    {
        return Inertia::render('Tramites/Create', [
            'workflows' => Workflow::all(),
        ]);
    }

    public function show(Tramite $tramite)
    {
        $workflow = Workflow::with(['pasos', 'transiciones'])->findOrFail($tramite->workflow_id);

        return Inertia::render('Tramites/Show', [
            'tramite' => $tramite->load('workflow'),
            'flujo' => [
                'pasos' => $workflow->pasos->map(fn($p) => [
                    'id' => $p->id,
                    'nombre' => $p->nombre,
                ]),
                'transiciones' => $workflow->transiciones->map(fn($t) => [
                    'origen' => $t->paso_origen_id,
                    'destino' => $t->paso_destino_id,
                    'accion' => $t->accion,
                ]),
            ],
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'codigo' => 'required|unique:tramites',
            'titulo' => 'required',
            'descripcion' => 'nullable',
            'workflow_id' => 'required|exists:workflows,id',
        ]);

        Tramite::create([
            'codigo' => $request->codigo,
            'titulo' => $request->titulo,
            'descripcion' => $request->descripcion,
            'workflow_id' => $request->workflow_id,
            'usuario_id' => Auth::id(),
        ]);

        return redirect()->route('tramites.index')->with('success', 'Trámite creado.');
    }

    public function edit(Tramite $tramite)
    {
        $workflow = Workflow::with(['pasos', 'transiciones'])->findOrFail($tramite->workflow_id);

        return Inertia::render('Tramites/Edit', [
            'tramite' => $tramite,
            'workflows' => Workflow::all(),
            'flujo' => [
                'pasos' => $workflow->pasos->map(fn($p) => [
                    'id' => $p->id,
                    'nombre' => $p->nombre,
                ]),
                'transiciones' => $workflow->transiciones->map(fn($t) => [
                    'origen' => $t->paso_origen_id,
                    'destino' => $t->paso_destino_id,
                    'accion' => $t->accion,
                ]),
            ],
        ]);
    }

    public function update(Request $request, Tramite $tramite)
    {
        $request->validate([
            'codigo' => 'required|unique:tramites,codigo,' . $tramite->id,
            'titulo' => 'required',
            'descripcion' => 'nullable',
            'workflow_id' => 'required|exists:workflows,id',
        ]);

        $tramite->update($request->only('codigo', 'titulo', 'descripcion', 'workflow_id'));

        return redirect()->route('tramites.index')->with('success', 'Trámite actualizado.');
    }

    public function destroy(Tramite $tramite)
    {
        $tramite->delete();

        return redirect()->back()->with('success', 'Trámite eliminado.');
    }
}
