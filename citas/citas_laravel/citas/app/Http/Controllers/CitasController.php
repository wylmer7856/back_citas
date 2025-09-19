<?php

namespace App\Http\Controllers;

use App\Models\Citas;
use Illuminate\Http\Request;

class CitasController extends Controller
{
    public function index()
    {
        return response()->json(
            Citas::with(['paciente', 'medico', 'historial'])->get(),
            200
        );
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_paciente' => 'required|exists:usuarios,id',
            'id_medico' => 'required|exists:usuarios,id',
            'fecha' => 'required|date',
            'hora' => 'required',
            'estado' => 'required|string'
        ]);

        $cita = Citas::create($request->all());

        return response()->json(
            $cita->load(['paciente', 'medico']),
            201
        );
    }

    public function show($id)
    {
        $cita = Citas::with(['paciente', 'medico', 'historial'])->find($id);

        if (!$cita) {
            return response()->json(['message' => 'Cita no encontrada'], 404);
        }

        return response()->json($cita, 200);
    }

    public function update(Request $request, $id)
    {
        $cita = Citas::find($id);

        if (!$cita) {
            return response()->json(['message' => 'Cita no encontrada'], 404);
        }

        $cita->update($request->only(['id_paciente', 'id_medico', 'fecha', 'hora', 'estado']));

        return response()->json(
            $cita->load(['paciente', 'medico', 'historial']),
            200
        );
    }

    public function destroy($id)
    {
        $cita = Citas::find($id);

        if (!$cita) {
            return response()->json(['message' => 'Cita no encontrada'], 404);
        }

        $cita->delete();

        return response()->json(['message' => 'Cita eliminada'], 200);
    }
}
