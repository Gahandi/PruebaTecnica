<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $roles): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();
        
        // Dividir los roles por coma y verificar si el usuario tiene alguno de ellos
        $allowedRoles = explode(',', $roles);
        $hasRole = false;
        
        foreach ($allowedRoles as $role) {
            if ($user->hasRole(trim($role))) {
                $hasRole = true;
                break;
            }
        }
        redirect()->route('events.public');
        if (!$hasRole) {
            abort(403, 'No tienes permisos para acceder a esta sección.');
        }

        return $next($request);
    }
}
