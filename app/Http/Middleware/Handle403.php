<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class Handle403
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);
        
        // Si la respuesta es 403, redirigir a la página principal
        if ($response->getStatusCode() === 403) {
            return redirect('/')->with('error', 'No tienes permisos para acceder a esta sección.');
        }
        
        return $response;
    }
}
