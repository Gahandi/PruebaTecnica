<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RedirectStaffToStaffRoutes
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check() && auth()->user()->hasRole('staff') && !auth()->user()->hasRole('admin')) {
            $path = $request->path();
            
            // Redirigir rutas de admin/checkins a staff/checkins
            if (str_starts_with($path, 'admin/checkins')) {
                $staffPath = str_replace('admin/checkins', 'staff/checkins', $path);
                return redirect()->to($staffPath);
            }
        }

        return $next($request);
    }
}