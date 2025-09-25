<?php

namespace App\Http\Controllers;

use App\Models\Medico_Especialidad;
use App\Models\Usuarios;
use Illuminate\Http\Request;

class MedicoEspecialidadController extends Controller
{
    /**
     * Listar relaciones médico-especialidad
     * Admin ve todas, médico solo las suyas
     */
    public function index(Request $request)
    {
        $usuario = $request->user();

        if ($usuario->rol === 'MEDICO') {
            $relaciones = Medico_Especialidad::with('especialidad')
                ->where('id_medico', $usuario->id)
                ->get();
        } elseif ($usuario->rol === 'ADMIN') {
            $relaciones = Medico_Especialidad::with(['medico', 'especialidad'])->get();
        } else {
            return response()->json(['error' => 'No autorizado'], 403);
        }

        return response()->json($relaciones, 200);
    }

    /**
     * Ver especialidades de un médico específico (Admin o el mismo médico)
     */
    public function porMedico($id_medico, Request $request)
    {
        $usuario = $request->user();

        if ($usuario->rol !== 'ADMIN' && $usuario->id !== (int) $id_medico) {
            return response()->json(['error' => 'No autorizado'], 403);
        }

        $relaciones = Medico_Especialidad::with('especialidad')
            ->where('id_medico', $id_medico)
            ->get();

        return response()->json($relaciones, 200);
    }

    /**
     * Asignar una especialidad a un médico (solo Admin)
     */
    public function store(Request $request)
    {
        if ($request->user()->rol !== 'ADMIN') {
            return response()->json(['error' => 'No autorizado'], 403);
        }

        $request->validate([
            'id_medico'       => 'required|exists:usuarios,id',
            'id_especialidad' => 'required|exists:especialidades,id'
        ]);

        $medico = Usuarios::find($request->id_medico);
        if ($medico->rol !== 'MEDICO') {
            return response()->json(['error' => 'El usuario no es un médico'], 400);
        }

        $existe = Medico_Especialidad::where('id_medico', $request->id_medico)
            ->where('id_especialidad', $request->id_especialidad)
            ->exists();

        if ($existe) {
            return response()->json(['error' => 'Ya existe esta relación'], 409);
        }

        $relacion = Medico_Especialidad::create([
            'id_medico' => $request->id_medico,
            'id_especialidad' => $request->id_especialidad
        ]);

        return response()->json($relacion->load(['medico', 'especialidad']), 201);
    }

    /**
     * Asignar múltiples especialidades a un médico (solo Admin)
     */
    public function asignarMultiples(Request $request)
    {
        if ($request->user()->rol !== 'ADMIN') {
            return response()->json(['error' => 'No autorizado'], 403);
        }

        $request->validate([
            'id_medico' => 'required|exists:usuarios,id',
            'especialidades' => 'required|array|min:1',
            'especialidades.*' => 'exists:especialidades,id'
        ]);

        $medico = Usuarios::find($request->id_medico);
        if ($medico->rol !== 'MEDICO') {
            return response()->json(['error' => 'El usuario no es un médico'], 400);
        }

        $asignadas = [];

        foreach ($request->especialidades as $id_especialidad) {
            $existe = Medico_Especialidad::where('id_medico', $request->id_medico)
                ->where('id_especialidad', $id_especialidad)
                ->exists();

            if (!$existe) {
                $relacion = Medico_Especialidad::create([
                    'id_medico' => $request->id_medico,
                    'id_especialidad' => $id_especialidad
                ]);
                $asignadas[] = $relacion->load('especialidad');
            }
        }

        return response()->json([
            'message' => 'Especialidades asignadas correctamente',
            'relaciones' => $asignadas
        ], 201);
    }

    /**
     * Eliminar relación médico-especialidad (solo Admin)
     */
    public function destroy($id, Request $request)
    {
        if ($request->user()->rol !== 'ADMIN') {
            return response()->json(['error' => 'No autorizado'], 403);
        }

        $relacion = Medico_Especialidad::find($id);

        if (!$relacion) {
            return response()->json(['error' => 'Relación no encontrada'], 404);
        }

        $relacion->delete();

        return response()->json(['message' => 'Relación eliminada'], 200);
    }
}
