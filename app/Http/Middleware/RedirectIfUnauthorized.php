<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfUnauthorized
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        try {
            $response = $next($request);
            return $response;
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            // Si es una petición AJAX, devolver JSON
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'No tienes permisos para acceder a esta sección.',
                    'redirect' => url('/')
                ], 403);
            }
            
            // Redirigir a la página principal con mensaje
            return redirect('/')->with('error', 'No tienes permisos para acceder a esta sección.');
        } catch (\Exception $e) {
            // Para otros errores, redirigir también
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Error de acceso.',
                    'redirect' => url('/')
                ], 403);
            }
            
            return redirect('/')->with('error', 'Error de acceso.');
        }
    }
}
