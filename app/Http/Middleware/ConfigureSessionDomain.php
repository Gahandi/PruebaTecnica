<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;

class ConfigureSessionDomain
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
        
        // Determinar el entorno
        $environment = app()->environment('local') ? 'local' : 'production';
        
        // Obtener el dominio base de la configuración
        $appUrl = config('app.url');
        $parsedUrl = parse_url($appUrl);
        $baseHost = $parsedUrl['host'] ?? 'boletos.local';
        
        // Configurar el dominio de sesión para que funcione en subdominios
        $sessionDomain = '.' . $baseHost;
        
        // Solo configurar si el host actual pertenece al dominio base
        if (str_contains($host, $baseHost)) {
            // Configurar el dominio de la sesión
            Config::set('session.domain', $sessionDomain);
            
            // Configurar el nombre de la cookie de sesión
            Config::set('session.cookie', env('SESSION_COOKIE', 'laravel_session'));
            
            // Configurar el path de la cookie
            Config::set('session.path', '/');
            
            // Configurar si es seguro (HTTPS)
            Config::set('session.secure', $request->secure());
            
            // Configurar HTTP only
            Config::set('session.http_only', true);
            
            // Configurar SameSite
            Config::set('session.same_site', 'lax');
        }
        
        return $next($request);
    }
}
