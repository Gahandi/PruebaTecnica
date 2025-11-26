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

        // Verificar si el usuario está verificado (verificar ambos campos)
        $isVerified = $user->verified_at || $user->verified;

        // Si el usuario no está verificado, redirigir a verificación
        // Excluir rutas de verificación, logout y públicas para evitar loops
        if (!$isVerified && !$request->routeIs('verify.*') && !$request->routeIs('logout') && !$request->routeIs('home') && !$request->routeIs('events.*')) {
            return redirect()->route('verify.email')
                ->with('warning', 'Por favor, verifica tu correo electrónico para continuar.');
        }

        return $next($request);
    }
}

