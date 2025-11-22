<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array<int, string>
     */
    protected $except = [
        //
    ];
    
    /**
     * Determine if the session and input CSRF tokens match.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return bool
     */
    protected function tokensMatch($request)
    {
        // Permitir peticiones desde subdominios del mismo dominio base
        $origin = $request->headers->get('Origin');
        $baseDomain = parse_url(config('app.url'), PHP_URL_HOST);
        
        if ($origin) {
            $originHost = parse_url($origin, PHP_URL_HOST);
            
            // Si es un subdominio del mismo dominio base, validar de manera más flexible
            if ($originHost !== $baseDomain && substr($originHost, -strlen('.' . $baseDomain)) === '.' . $baseDomain) {
                // Para subdominios, verificar que el token esté presente
                $token = $request->input('_token') ?: $request->header('X-CSRF-TOKEN');
                return !empty($token) && hash_equals($request->session()->token(), $token);
            }
        }
        
        return parent::tokensMatch($request);
    }
}
