<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class HandleCorsForCart
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $origin = $request->headers->get('Origin');
        $baseDomain = parse_url(config('app.url'), PHP_URL_HOST);
        
        // Permitir peticiones desde subdominios del mismo dominio base
        $allowed = false;
        if ($origin) {
            $originHost = parse_url($origin, PHP_URL_HOST);
            
            // Permitir el mismo dominio
            if ($originHost === $baseDomain) {
                $allowed = true;
            }
            
            // Permitir subdominios del mismo dominio base
            if (substr($originHost, -strlen('.' . $baseDomain)) === '.' . $baseDomain) {
                $allowed = true;
            }
            
            // Permitir en localhost para desarrollo
            if (app()->environment('local') && (str_contains($originHost, 'localhost') || str_contains($originHost, '127.0.0.1'))) {
                $allowed = true;
            }
        }
        
        $response = $next($request);
        
        if ($allowed && $origin) {
            $response->headers->set('Access-Control-Allow-Origin', $origin);
            $response->headers->set('Access-Control-Allow-Credentials', 'true');
            $response->headers->set('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
            $response->headers->set('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With, X-CSRF-TOKEN');
        }
        
        // Manejar preflight requests
        if ($request->getMethod() === 'OPTIONS') {
            return response('', 200);
        }
        
        return $response;
    }
}

