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
        
        // PRIMERO: Verificar si es staff intentando acceder a rutas de admin y redirigir
        if ($user->hasRole('staff') && !$user->hasRole('admin')) {
            $path = $request->path();
            
            // Redirigir rutas de admin/checkins a staff/checkins
            if (str_starts_with($path, 'admin/checkins')) {
                $staffPath = str_replace('admin/checkins', 'staff/checkins', $path);
                return redirect()->to($staffPath);
            }
        }
        
        // SEGUNDO: Verificar roles normales
        $allowedRoles = explode(',', $roles);
        $hasRole = false;
        
        foreach ($allowedRoles as $role) {
            $role = trim($role);
            if ($user->hasRole($role)) {
                $hasRole = true;
                break;
            }
        }
        
        if (!$hasRole) {
            abort(403, 'No tienes permisos para acceder a esta sección.');
        }

        return $next($request);
    }
}
