<?php

namespace App\Http\Controllers;

use App\Models\Historial;
use Illuminate\Http\Request;

class HistorialController extends Controller
{
    /**
     * Listar historiales
     * - Admin ve todos
     * - Médico ve los historiales de sus citas
     * - Paciente ve sus propios historiales
     */
    public function index(Request $request)
    {
        $usuario = $request->user();

        if ($usuario->rol === 'admin') {
            $historiales = Historial::with('cita.medico', 'cita.paciente')->get();
        } elseif ($usuario->rol === 'medico') {
            $historiales = Historial::whereHas('cita', function ($q) use ($usuario) {
                $q->where('id_medico', $usuario->id);
            })->with('cita.paciente')->get();
        } else { // paciente
            $historiales = Historial::whereHas('cita', function ($q) use ($usuario) {
                $q->where('id_paciente', $usuario->id);
            })->with('cita.medico')->get();
        }

        return response()->json($historiales, 200);
    }

    /**
     * Crear historial (solo médico)
     */
    public function store(Request $request)
    {
        $usuario = $request->user();
        if ($usuario->rol !== 'medico') {
            return response()->json(['message' => 'No tienes permisos para crear historiales'], 403);
        }

        $request->validate([
            'id_cita'      => 'required|exists:citas,id',
            'diagnostico'  => 'nullable|string',
            'receta'       => 'nullable|string',
            'observaciones'=> 'nullable|string'
        ]);

        $historial = Historial::create($request->only(['id_cita', 'diagnostico', 'receta', 'observaciones']));

        return response()->json($historial->load('cita'), 201);
    }

    /**
     * Ver historial
     * - Admin puede ver todos
     * - Médico solo los suyos
     * - Paciente solo los suyos
     */
    public function show($id, Request $request)
    {
        $usuario = $request->user();

        $historial = Historial::with('cita.medico', 'cita.paciente')->find($id);

        if (!$historial) {
            return response()->json(['message' => 'Historial no encontrado'], 404);
        }

        if ($usuario->rol === 'admin') {
            return response()->json($historial, 200);
        }

        if ($usuario->rol === 'medico' && $historial->cita->id_medico === $usuario->id) {
            return response()->json($historial, 200);
        }

        if ($usuario->rol === 'paciente' && $historial->cita->id_paciente === $usuario->id) {
            return response()->json($historial, 200);
        }

        return response()->json(['message' => 'No tienes permisos para ver este historial'], 403);
    }

    /**
     * Actualizar historial (solo médico dueño de la cita)
     */
    public function update(Request $request, $id)
    {
        $usuario = $request->user();
        $historial = Historial::with('cita')->find($id);

        if (!$historial) {
            return response()->json(['message' => 'Historial no encontrado'], 404);
        }

        if ($usuario->rol !== 'medico' || $historial->cita->id_medico !== $usuario->id) {
            return response()->json(['message' => 'No tienes permisos para actualizar este historial'], 403);
        }

        $historial->update($request->only(['diagnostico', 'receta', 'observaciones']));

        return response()->json($historial, 200);
    }

    /**
     * Eliminar historial (solo admin)
     */
    public function destroy($id, Request $request)
    {
        $usuario = $request->user();

        if ($usuario->rol !== 'admin') {
            return response()->json(['message' => 'Solo el admin puede eliminar historiales'], 403);
        }

        $historial = Historial::find($id);

        if (!$historial) {
            return response()->json(['message' => 'Historial no encontrado'], 404);
        }

        $historial->delete();

        return response()->json(['message' => 'Historial eliminado'], 200);
    }
}
