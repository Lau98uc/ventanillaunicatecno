<?php

namespace App\Http\Controllers;

use App\Models\Documento;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DocumentoController extends Controller
{
    public function index()
    {
        $documentos = Documento::with('solicitud')->paginate(10);
        
        return Inertia::render('Documentos/Index', [
            'documentos' => $documentos,
        ]);
    }

    public function create()
    {
        return Inertia::render('Documentos/Create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'path' => 'required|string',
            'solicitud_id' => 'nullable|exists:solicitudes_tramite,id',
        ]);

        Documento::create($request->all());

        return redirect()->route('documentos.index')->with('success', 'Documento creado exitosamente.');
    }

    public function show(Documento $documento)
    {
        return Inertia::render('Documentos/Show', [
            'documento' => $documento->load('solicitud'),
        ]);
    }

    public function edit(Documento $documento)
    {
        return Inertia::render('Documentos/Edit', [
            'documento' => $documento->load('solicitud'),
        ]);
    }

    public function update(Request $request, Documento $documento)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'path' => 'required|string',
            'solicitud_id' => 'nullable|exists:solicitudes_tramite,id',
        ]);

        $documento->update($request->all());

        return redirect()->route('documentos.index')->with('success', 'Documento actualizado exitosamente.');
    }

    public function destroy(Documento $documento)
    {
        $documento->delete();

        return redirect()->route('documentos.index')->with('success', 'Documento eliminado exitosamente.');
    }
}
