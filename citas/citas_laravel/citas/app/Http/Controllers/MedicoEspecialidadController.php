<?php

namespace App\Http\Controllers;

use App\Models\Medico_Especialidad;
use App\Models\Usuarios;
use Illuminate\Http\Request;

class MedicoEspecialidadController extends Controller
{
    /**
     * Listar todas las relaciones médico - especialidad
     * (solo Admin puede ver todos, médico solo ve las suyas)
     */
    public function index(Request $request)
    {
        $usuario = $request->user();

        if ($usuario->rol === 'medico') {
            // El médico solo ve sus propias especialidades
            $relaciones = Medico_Especialidad::with('especialidad')
                ->where('id_medico', $usuario->id)
                ->get();
        } else {
            // Admin puede ver todas
            $relaciones = Medico_Especialidad::with(['medico', 'especialidad'])->get();
        }

        return response()->json($relaciones, 200);
    }

    /**
     * Asignar una especialidad a un médico
     * (solo Admin puede asignar)
     */
    public function store(Request $request)
    {
        $request->validate([
            'id_medico'      => 'required|exists:usuarios,id',
            'id_especialidad'=> 'required|exists:especialidades,id'
        ]);

        // Validar que el usuario sea un médico
        $medico = Usuarios::find($request->id_medico);
        if ($medico->rol !== 'medico') {
            return response()->json(['message' => 'El usuario no es un médico'], 400);
        }

        // Evitar duplicados
        $existe = Medico_Especialidad::where('id_medico', $request->id_medico)
            ->where('id_especialidad', $request->id_especialidad)
            ->first();

        if ($existe) {
            return response()->json(['message' => 'Ya existe esta relación'], 409);
        }

        $relacion = Medico_Especialidad::create($request->only(['id_medico', 'id_especialidad']));

        return response()->json($relacion->load(['medico', 'especialidad']), 201);
    }

    /**
     * Eliminar relación médico - especialidad
     * (solo Admin puede eliminar)
     */
    public function destroy($id)
    {
        $relacion = Medico_Especialidad::find($id);

        if (!$relacion) {
            return response()->json(['message' => 'Relación no encontrada'], 404);
        }

        $relacion->delete();

        return response()->json(['message' => 'Relación eliminada'], 200);
    }
}
