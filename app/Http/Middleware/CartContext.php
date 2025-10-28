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
            $host = $request->getHost();
            $isSubdomain = CartHelper::isSubdomain($host);
            
            if ($isSubdomain) {
                // En subdominio: filtrar carrito para mostrar solo boletos del espacio actual
                $filteredCart = CartHelper::getFilteredCart($host);
                session()->put('cart', $filteredCart);
            }
            // En dominio principal: mantener el carrito completo (no se necesita filtrado)
            
            return $next($request);
        } catch (\Exception $e) {
            // Si hay error en el middleware, continuar sin filtrado
            \Log::error('CartContext middleware error: ' . $e->getMessage());
            return $next($request);
        }
    }
}
