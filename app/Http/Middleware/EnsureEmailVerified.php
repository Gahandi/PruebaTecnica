<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureEmailVerified
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();

        // Si el usuario no está verificado, redirigir a verificación
        // Excluir rutas de verificación para evitar loops
        if (!$user->verified_at && !$request->routeIs('verify.*')) {
            return redirect()->route('verify.email')
                ->with('warning', 'Por favor, verifica tu correo electrónico para continuar.');
        }

        return $next($request);
    }
}

