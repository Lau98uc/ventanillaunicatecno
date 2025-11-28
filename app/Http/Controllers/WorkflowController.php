<?php

namespace App\Http\Controllers;

use App\Models\Workflow;
use Illuminate\Http\Request;
use Inertia\Inertia;

class WorkflowController extends Controller
{
    public function index(Request $request)
    {
        $workflows = Workflow::with(['pasos', 'transiciones'])
            ->when($request->search, function($query, $search) {
                $query->where('nombre', 'ilike', "%{$search}%")
                      ->orWhere('descripcion', 'ilike', "%{$search}%");
            })
            ->withCount(['pasos', 'transiciones'])
            ->orderBy('nombre')
            ->paginate(10)
            ->withQueryString();
        
        return Inertia::render('Workflows/Index', [
            'filters' => $request->only('search'),
            'workflows' => $workflows,
        ]);
    }

    public function create()
    {
        return Inertia::render('Workflows/Create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
        ]);

        Workflow::create($request->all());

        return redirect()->route('workflows.index')->with('success', 'Workflow creado exitosamente.');
    }

    public function show(Workflow $workflow)
    {
        return Inertia::render('Workflows/Show', [
            'workflow' => $workflow->load(['pasos', 'transiciones']),
        ]);
    }

    public function edit(Workflow $workflow)
    {
        return Inertia::render('Workflows/Edit', [
            'workflow' => $workflow->load(['pasos', 'transiciones']),
        ]);
    }

    public function update(Request $request, Workflow $workflow)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
        ]);

        $workflow->update($request->all());

        return redirect()->route('workflows.index')->with('success', 'Workflow actualizado exitosamente.');
    }

    public function destroy(Workflow $workflow)
    {
        $workflow->delete();

        return redirect()->route('workflows.index')->with('success', 'Workflow eliminado exitosamente.');
    }
} 