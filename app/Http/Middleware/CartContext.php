<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Helpers\CartHelper;

class CartContext
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        try {
            // El carrito es global - no se filtra por espacio
            // La sesión se comparte entre dominio base y subdominios
            // Solo nos aseguramos de que el carrito exista en la sesión
            if (!session()->has('cart')) {
                session()->put('cart', []);
            }
            
            return $next($request);
        } catch (\Exception $e) {
            // Si hay error en el middleware, continuar sin filtrado
            \Log::error('CartContext middleware error: ' . $e->getMessage());
            return $next($request);
        }
    }
}
