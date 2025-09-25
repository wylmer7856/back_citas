<?php

namespace App\Http\Controllers;

use App\Models\Citas;
use Illuminate\Http\Request;

class CitasController extends Controller
{
    /**
     * Listar citas según rol
     */
    public function index(Request $request)
    {
        $usuario = $request->user();

        if ($usuario->rol === 'ADMIN') {
            $citas = Citas::with(['paciente', 'medico', 'historial'])->get();
        } elseif ($usuario->rol === 'MEDICO') {
            $citas = Citas::where('id_medico', $usuario->id)
                ->with(['paciente', 'historial'])->get();
        } elseif ($usuario->rol === 'PACIENTE') {
            $citas = Citas::where('id_paciente', $usuario->id)
                ->with(['medico', 'historial'])->get();
        } else {
            return response()->json(['error' => 'Rol no autorizado'], 403);
        }

        return response()->json($citas);
    }

    /**
     * Crear cita (solo paciente)
     */
    public function store(Request $request)
    {
        $usuario = $request->user();
        if ($usuario->rol !== 'PACIENTE') {
            return response()->json(['error' => 'Solo los pacientes pueden agendar citas'], 403);
        }

        $request->validate([
            
            'id_medico' => 'required|exists:usuarios,id',
            'fecha'     => 'required|date',
            'hora'      => 'required',
        ]);

        $cita = Citas::create([
            'id_paciente' => $usuario->id,
            'id_medico'   => $request->id_medico,
            'fecha'       => $request->fecha,
            'hora'        => $request->hora,
            'estado'      => 'PENDIENTE',
        ]);

        return response()->json($cita->load(['medico']), 201);
    }

    /**
     * Ver cita específica (según rol y propiedad)
     */
    public function show($id, Request $request)
    {
        $usuario = $request->user();
        $cita = Citas::with(['paciente', 'medico', 'historial'])->find($id);

        if (!$cita) {
            return response()->json(['message' => 'Cita no encontrada'], 404);
        }

        if (
            $usuario->rol === 'ADMIN' ||
            ($usuario->rol === 'MEDICO' && $cita->id_medico === $usuario->id) ||
            ($usuario->rol === 'PACIENTE' && $cita->id_paciente === $usuario->id)
        ) {
            return response()->json($cita);
        }

        return response()->json(['error' => 'No autorizado'], 403);
    }

    /**
     * Actualizar cita (solo admin o paciente dueño)
     */
    public function update(Request $request, $id)
    {
        $usuario = $request->user();
        $cita = Citas::find($id);

        if (!$cita) {
            return response()->json(['message' => 'Cita no encontrada'], 404);
        }

        if (
            $usuario->rol !== 'ADMIN' &&
            !($usuario->rol === 'PACIENTE' && $cita->id_paciente === $usuario->id)
        ) {
            return response()->json(['error' => 'No autorizado'], 403);
        }

        $request->validate([
            'fecha'  => 'nullable|date',
            'hora'   => 'nullable',
            'estado' => 'nullable|in:PENDIENTE,CONFIRMADA,CANCELADA,ATENDIDA'
        ]);

        $cita->update($request->only(['fecha', 'hora', 'estado']));

        return response()->json($cita->load(['paciente', 'medico', 'historial']));
    }

    /**
     * Eliminar cita (solo admin o paciente dueño)
     */
    public function destroy($id, Request $request)
    {
        $usuario = $request->user();
        $cita = Citas::find($id);

        if (!$cita) {
            return response()->json(['message' => 'Cita no encontrada'], 404);
        }

        if (
            $usuario->rol !== 'ADMIN' &&
            !($usuario->rol === 'PACIENTE' && $cita->id_paciente === $usuario->id)
        ) {
            return response()->json(['error' => 'No autorizado'], 403);
        }

        $cita->delete();

        return response()->json(['message' => 'Cita eliminada']);
    }

    /**
     * Ver mis citas (médico o paciente)
     */
    public function misCitas(Request $request)
    {
        $usuario = $request->user();

        if ($usuario->rol === 'MEDICO') {
            $citas = Citas::where('id_medico', $usuario->id)->with('paciente')->get();
        } elseif ($usuario->rol === 'PACIENTE') {
            $citas = Citas::where('id_paciente', $usuario->id)->with('medico')->get();
        } else {
            return response()->json(['error' => 'No autorizado'], 403);
        }

        return response()->json($citas);
    }
}
