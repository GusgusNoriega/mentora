<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckAdminRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Verificar si el usuario está autenticado
        if (!$request->user()) {
            return response()->json([
                'success' => false,
                'data' => null,
                'meta' => [
                    'message' => 'Usuario no autenticado',
                ],
            ], 401);
        }

        // Verificar si el usuario tiene el rol 'admin'
        if (!$request->user()->hasRole('admin')) {
            return response()->json([
                'success' => false,
                'data' => null,
                'meta' => [
                    'message' => 'Acceso denegado. Se requiere rol de administrador',
                ],
            ], 403);
        }

        return $next($request);
    }
}