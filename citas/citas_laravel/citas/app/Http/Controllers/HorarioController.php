<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Horario;
use App\Models\Usuarios;
use Illuminate\Http\Request;

class HorarioController extends Controller
{
    // Mostrar todos los horarios (solo disponibles)
    public function index()
    {
        $horarios = Horario::with('usuario')
            ->where('disponible', true)
            ->orderBy('fecha')
            ->orderBy('hora_inicio')
            ->get();

        return response()->json($horarios);
    }

    // Crear un nuevo horario
    public function store(Request $request)
    {
        $request->validate([
            'id_usuario' => 'required|exists:usuarios,id',
            'fecha' => 'required|date',
            'hora_inicio' => 'required|date_format:H:i',
            'hora_fin' => 'required|date_format:H:i|after:hora_inicio',
        ]);

        $usuario = Usuarios::find($request->id_usuario);
        if ($usuario->tipo !== 'MEDICO') {
            return response()->json(['error' => 'El usuario no es un médico'], 403);
        }

        $horario = Horario::create($request->all());

        return response()->json($horario, 201);
    }

    // Mostrar un horario específico
    public function show($id)
    {
        $horario = Horario::with('usuario')->find($id);

        if (!$horario) {
            return response()->json(['error' => 'Horario no encontrado'], 404);
        }

        return response()->json($horario);
    }

    // Actualizar un horario
    public function update(Request $request, $id)
    {
        $horario = Horario::find($id);

        if (!$horario) {
            return response()->json(['error' => 'Horario no encontrado'], 404);
        }

        $request->validate([
            'fecha' => 'sometimes|date',
            'hora_inicio' => 'sometimes|date_format:H:i',
            'hora_fin' => 'sometimes|date_format:H:i|after:hora_inicio',
            'disponible' => 'sometimes|boolean',
        ]);

        $horario->update($request->all());

        return response()->json($horario);
    }

    // Eliminar un horario
    public function destroy($id)
    {
        $horario = Horario::find($id);

        if (!$horario) {
            return response()->json(['error' => 'Horario no encontrado'], 404);
        }

        $horario->delete();

        return response()->json(['message' => 'Horario eliminado']);
    }

    // Mostrar horarios por médico
    public function porMedico($id_usuario)
    {
        $usuario = Usuarios::find($id_usuario);

        if (!$usuario || $usuario->tipo !== 'MEDICO') {
            return response()->json(['error' => 'Médico no válido'], 404);
        }

        $horarios = Horario::where('id_usuario', $id_usuario)
            ->orderBy('fecha')
            ->orderBy('hora_inicio')
            ->get();

        return response()->json($horarios);
    }

    // Marcar horario como no disponible
    public function bloquear($id)
    {
        $horario = Horario::find($id);

        if (!$horario) {
            return response()->json(['error' => 'Horario no encontrado'], 404);
        }

        $horario->disponible = false;
        $horario->save();

        return response()->json(['message' => 'Horario bloqueado']);
    }
}
