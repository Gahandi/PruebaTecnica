<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SpaceAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Obtener el subdominio de la URL
        $host = $request->getHost();
        $subdomain = explode('.', $host)[0];
        
        // Buscar el espacio por subdomain
        $space = \App\Models\Space::where('subdomain', $subdomain)->first();
        
        if (!$space) {
            abort(404, 'Espacio no encontrado');
        }
        
        // Verificar que el usuario esté autenticado
        if (!auth()->check()) {
            return redirect()->route('login')->with('error', 'Debes iniciar sesión para acceder a esta sección');
        }
        
        // Verificar que el usuario es admin del space
        if (!auth()->user()->isAdminOfSpace($space->id)) {
            abort(403, 'No tienes permisos de administrador para acceder a esta sección');
        }
        
        // Agregar el espacio a la request para uso posterior
        $request->merge(['space' => $space]);
        
        return $next($request);
    }
}

