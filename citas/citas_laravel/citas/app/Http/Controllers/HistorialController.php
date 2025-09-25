<?php

namespace App\Http\Controllers;

use App\Models\Citas;
use App\Models\Historial;
use App\Models\Cita;
use Illuminate\Http\Request;

class HistorialController extends Controller
{
    /**
     * Listar historiales según rol
     */
    public function index(Request $request)
    {
        $usuario = $request->user();

        if ($usuario->rol === 'ADMIN') {
            $historiales = Historial::with('cita.medico', 'cita.paciente')->get();
        } elseif ($usuario->rol === 'MEDICO') {
            $historiales = Historial::whereHas('cita', fn($q) => $q->where('id_medico', $usuario->id))
                ->with('cita.paciente')->get();
        } elseif ($usuario->rol === 'PACIENTE') {
            $historiales = Historial::whereHas('cita', fn($q) => $q->where('id_paciente', $usuario->id))
                ->with('cita.medico')->get();
        } else {
            abort(403, 'Rol no autorizado');
        }

        return response()->json($historiales);
    }

    /**
     * Crear historial (solo médico dueño de la cita)
     */
    public function store(Request $request)
    {
        $usuario = $request->user();
        abort_unless($usuario->rol === 'MEDICO', 403, 'Solo los médicos pueden crear historiales');

        $request->validate([
            'id_cita'       => 'required|exists:citas,id',
            'diagnostico'   => 'nullable|string',
            'receta'        => 'nullable|string',
            'observaciones' => 'nullable|string'
        ]);

        $cita = Citas::find($request->id_cita);
        abort_unless($cita && $cita->id_medico === $usuario->id, 403, 'No puedes crear historial para esta cita');

        $historial = Historial::create($request->only(['id_cita', 'diagnostico', 'receta', 'observaciones']));

        return response()->json($historial->load('cita'), 201);
    }

    /**
     * Ver historial
     */
    public function show($id, Request $request)
    {
        $usuario = $request->user();
        $historial = Historial::with('cita.medico', 'cita.paciente')->find($id);
        abort_if(!$historial, 404, 'Historial no encontrado');

        $cita = $historial->cita;

        if (
            $usuario->rol === 'ADMIN' ||
            ($usuario->rol === 'MEDICO' && $cita->id_medico === $usuario->id) ||
            ($usuario->rol === 'PACIENTE' && $cita->id_paciente === $usuario->id)
        ) {
            return response()->json($historial);
        }

        abort(403, 'No tienes permisos para ver este historial');
    }

    /**
     * Actualizar historial (solo médico dueño de la cita)
     */
    public function update(Request $request, $id)
    {
        $usuario = $request->user();
        $historial = Historial::with('cita')->find($id);
        abort_if(!$historial, 404, 'Historial no encontrado');

        abort_unless($usuario->rol === 'MEDICO' && $historial->cita->id_medico === $usuario->id,
            403, 'No tienes permisos para actualizar este historial');

        $historial->update($request->only(['diagnostico', 'receta', 'observaciones']));

        return response()->json($historial);
    }

    /**
     * Eliminar historial (solo admin)
     */
    public function destroy($id, Request $request)
    {
        abort_unless($request->user()->rol === 'ADMIN', 403, 'Solo el admin puede eliminar historiales');

        $historial = Historial::find($id);
        abort_if(!$historial, 404, 'Historial no encontrado');

        $historial->delete();

        return response()->json(['message' => 'Historial eliminado']);
    }

    /**
     * Ver historial por cita (Admin, médico o paciente)
     */
    public function porCita($id_cita, Request $request)
    {
        $usuario = $request->user();
        $historial = Historial::where('id_cita', $id_cita)->with('cita.medico', 'cita.paciente')->first();
        abort_if(!$historial, 404, 'Historial no encontrado');

        $cita = $historial->cita;

        if (
            $usuario->rol === 'ADMIN' ||
            ($usuario->rol === 'MEDICO' && $cita->id_medico === $usuario->id) ||
            ($usuario->rol === 'PACIENTE' && $cita->id_paciente === $usuario->id)
        ) {
            return response()->json($historial);
        }

        abort(403, 'No tienes permisos para ver este historial');
    }
}
