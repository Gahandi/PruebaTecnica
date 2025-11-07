<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;

class SubdomainSession
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        // Obtener el host de la request
        $host = $request->getHost();
        
        // Determinar el entorno y configurar dominios
        $environment = app()->environment('local') ? 'local' : 'production';
        $baseDomain = config("subdomain.domain.{$environment}");
        $sessionDomain = config("subdomain.session.domain.{$environment}");
        
        // Configurar el dominio de la sesión para que funcione en subdominios
        if (str_contains($host, $baseDomain)) {
            // Configurar el dominio de la sesión con punto inicial para subdominios
            Config::set('session.domain', $sessionDomain);
            
            // También configurar el dominio de las cookies
            Config::set('session.cookie', $sessionDomain);
        }
        
        return $next($request);
    }
}
