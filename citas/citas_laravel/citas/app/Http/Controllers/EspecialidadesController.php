<?php

namespace App\Http\Controllers;

use App\Models\Especialidades;
use Illuminate\Http\Request;

class EspecialidadesController extends Controller
{
    /**
     * Listar todas las especialidades (acceso libre)
     */
    public function index()
    {
        $especialidades = Especialidades::orderBy('nombre')->get();
        return response()->json($especialidades);
    }

    /**
     * Buscar especialidad por nombre (acceso libre)
     */
    public function buscarPorNombre(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string'
        ]);

        $especialidad = Especialidades::where('nombre', 'like', '%' . $request->nombre . '%')->get();

        return response()->json($especialidad);
    }

    /**
     * Crear especialidad (solo Admin)
     */
    public function store(Request $request)
    {
        abort_unless($request->user()->rol === 'ADMIN', 403, 'Solo el administrador puede crear especialidades');

        $request->validate([
            'nombre' => 'required|string|unique:especialidades,nombre'
        ]);

        $especialidad = Especialidades::create([
            'nombre' => ucfirst(strtolower($request->nombre))
        ]);

        return response()->json([
            'message' => 'Especialidad creada correctamente',
            'data' => $especialidad
        ], 201);
    }

    /**
     * Mostrar una especialidad por ID
     */
    public function show($id)
    {
        $especialidad = Especialidades::find($id);
        abort_if(!$especialidad, 404, 'Especialidad no encontrada');

        return response()->json($especialidad);
    }

    /**
     * Actualizar especialidad (solo Admin)
     */
    public function update(Request $request, $id)
    {
        abort_unless($request->user()->rol === 'ADMIN', 403, 'Solo el administrador puede actualizar especialidades');

        $especialidad = Especialidades::find($id);
        abort_if(!$especialidad, 404, 'Especialidad no encontrada');

        $request->validate([
            'nombre' => 'required|string|unique:especialidades,nombre,' . $id
        ]);

        $especialidad->update([
            'nombre' => ucfirst(strtolower($request->nombre))
        ]);

        return response()->json([
            'message' => 'Especialidad actualizada correctamente',
            'data' => $especialidad
        ]);
    }

    /**
     * Eliminar especialidad (solo Admin)
     */
    public function destroy($id, Request $request)
    {
        abort_unless($request->user()->rol === 'ADMIN', 403, 'Solo el administrador puede eliminar especialidades');

        $especialidad = Especialidades::find($id);
        abort_if(!$especialidad, 404, 'Especialidad no encontrada');

        $especialidad->delete();

        return response()->json(['message' => 'Especialidad eliminada']);
    }
}
