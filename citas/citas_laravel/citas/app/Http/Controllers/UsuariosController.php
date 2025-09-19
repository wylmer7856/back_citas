<?php

namespace App\Http\Controllers;

use App\Models\Usuarios;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UsuariosController extends Controller
{
    /**
     * Listar todos los usuarios (solo Admin)
     */
    public function index()
    {
        return response()->json(Usuarios::all(), 200);
    }

    /**
     * Crear usuario (solo Admin)
     */
    public function store(Request $request)
    {
        $request->validate([
            'nombre'   => 'required|string',
            'apellido' => 'required|string',
            'email'    => 'required|email|unique:usuarios',
            'telefono' => 'nullable|string',
            'rol'      => 'required|in:admin,medico,paciente',
            'password' => 'required|string|min:6'
        ]);

        $usuario = Usuarios::create([
            'nombre'   => $request->nombre,
            'apellido' => $request->apellido,
            'email'    => $request->email,
            'telefono' => $request->telefono,
            'rol'      => $request->rol,
            'password' => Hash::make($request->password),
        ]);

        return response()->json($usuario, 201);
    }

    /**
     * Mostrar un usuario
     */
    public function show($id)
    {
        $usuario = Usuarios::find($id);

        if (!$usuario) {
            return response()->json(['message' => 'Usuario no encontrado'], 404);
        }

        return response()->json($usuario, 200);
    }

    /**
     * Actualizar usuario
     */
    public function update(Request $request, $id)
    {
        $usuario = Usuarios::find($id);

        if (!$usuario) {
            return response()->json(['message' => 'Usuario no encontrado'], 404);
        }

        $request->validate([
            'email' => 'nullable|email|unique:usuarios,email,' . $usuario->id,
            'rol'   => 'nullable|in:admin,medico,paciente'
        ]);

        $usuario->update($request->only(['nombre', 'apellido', 'email', 'telefono', 'rol']));

        if ($request->filled('password')) {
            $usuario->password = Hash::make($request->password);
            $usuario->save();
        }

        return response()->json($usuario, 200);
    }

    /**
     * Eliminar usuario
     */
    public function destroy($id)
    {
        $usuario = Usuarios::find($id);

        if (!$usuario) {
            return response()->json(['message' => 'Usuario no encontrado'], 404);
        }

        $usuario->delete();

        return response()->json(['message' => 'Usuario eliminado'], 200);
    }

    /**
     * Registro de paciente (registro público)
     */
    public function register(Request $request)
    {
        $request->validate([
            'nombre'   => 'required|string',
            'apellido' => 'required|string',
            'email'    => 'required|email|unique:usuarios',
            'telefono' => 'nullable|string',
            'password' => 'required|string|min:6',
        ]);

        $usuario = Usuarios::create([
            'nombre'   => $request->nombre,
            'apellido' => $request->apellido,
            'email'    => $request->email,
            'telefono' => $request->telefono,
            'rol'      => 'paciente', // 👈 el rol de los registros públicos siempre será paciente
            'password' => Hash::make($request->password),
        ]);

        return response()->json([
            'message' => 'Paciente registrado exitosamente',
            'usuario' => $usuario
        ], 201);
    }

    /**
     * Login con Sanctum
     */
    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string'
        ]);

        $usuario = Usuarios::where('email', $request->email)->first();

        if (!$usuario || !Hash::check($request->password, $usuario->password)) {
            return response()->json(['message' => 'Credenciales inválidas'], 401);
        }

        $token = $usuario->createToken('auth_token')->plainTextToken;

        return response()->json([
            'access_token' => $token,
            'token_type'   => 'Bearer',
            'usuario'      => $usuario
        ]);
    }

    /**
     * Logout
     */
    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();

        return response()->json(['message' => 'Sesión cerrada correctamente']);
    }
}
