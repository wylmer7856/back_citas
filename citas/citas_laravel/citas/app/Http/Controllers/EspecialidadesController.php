<?php

namespace App\Http\Controllers;

use App\Models\Especialidades;
use Illuminate\Http\Request;

class EspecialidadesController extends Controller
{
    public function index()
    {
        return response()->json(Especialidades::all(), 200);
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|unique:especialidades'
        ]);

        $especialidad = Especialidades::create($request->all());

        return response()->json([
            'message' => 'Especialidad creada correctamente',
            'data' => $especialidad
        ], 201);
    }

    public function show($id)
    {
        $especialidad = Especialidades::findOrFail($id);
        return response()->json($especialidad, 200);
    }

    public function update(Request $request, $id)
    {
        $especialidad = Especialidades::findOrFail($id);

        $request->validate([
            'nombre' => 'required|string|unique:especialidades,nombre,' . $id
        ]);

        $especialidad->update($request->only(['nombre']));

        return response()->json([
            'message' => 'Especialidad actualizada correctamente',
            'data' => $especialidad
        ], 200);
    }

    public function destroy($id)
    {
        $especialidad = Especialidades::findOrFail($id);
        $especialidad->delete();

        return response()->json(['message' => 'Especialidad eliminada'], 200);
    }
}
