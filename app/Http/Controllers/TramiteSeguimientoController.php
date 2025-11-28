<?php

namespace App\Http\Controllers;

use App\Models\Tramite;
use Illuminate\Http\Request;

class TramiteSeguimientoController extends Controller
{
    public function store(Request $request, Tramite $tramite)
    {
        $request->validate([
            'estado_nuevo' => 'required|string',
            'observacion' => 'nullable|string',
        ]);

        $tramite->seguimientos()->create([
            'usuario_id' => auth()->id(),
            'estado_anterior' => $tramite->estado,
            'estado_nuevo' => $request->estado_nuevo,
            'observacion' => $request->observacion,
        ]);

        $tramite->update(['estado' => $request->estado_nuevo]);

        return redirect()->back()->with('success', 'Seguimiento registrado.');
    }
}
