<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RedirectAfterLogin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check()) {
            $user = auth()->user();
            
            // Redirigir según el rol del usuario
            if ($user->hasRole('admin')) {
                return redirect()->route('dashboard');
            } elseif ($user->hasRole('staff')) {
                return redirect()->route('dashboard');
            } else {
                // Para viewers y otros roles, ir a la página principal
                return redirect()->route('events.public');
            }
        }
        
        return $next($request);
    }
}
