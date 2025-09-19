<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        $user = Auth::user();

        // Si no hay usuario autenticado
        if (!$user) {
            return response()->json(['error' => 'No autenticado'], 401);
        }

        // Si el rol del usuario no está dentro de los permitidos
        if (!in_array($user->rol, $roles)) {
            return response()->json(['error' => 'No tienes permisos'], 403);
        }

        return $next($request);
    }
}
