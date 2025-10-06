<?php

namespace App\Helpers;

use App\Models\Space;
use App\Models\TicketType;

class CartHelper
{
    /**
     * Obtener el carrito filtrado según el contexto
     */
    public static function getFilteredCart($host = null)
    {
        $host = $host ?: request()->getHost();
        $isSubdomain = self::isSubdomain($host);
        
        $cart = session()->get('cart', []);
        
        if ($isSubdomain) {
            $subdomain = explode('.', $host)[0];
            $space = Space::where('subdomain', $subdomain)->first();
            
            if ($space) {
                return self::filterCartBySpace($cart, $space->id);
            }
        }
        
        return $cart;
    }
    
    /**
     * Verificar si es un subdominio
     */
    public static function isSubdomain($host)
    {
        $parts = explode('.', $host);
        return count($parts) > 2; // Ej: prueba-de-cajon-2.boletos.local
    }
    
    /**
     * Filtrar carrito para mostrar solo boletos del espacio actual
     */
    public static function filterCartBySpace($cart, $spaceId)
    {
        $filteredCart = [];
        
        foreach ($cart as $ticketTypeId => $item) {
            // Si el item tiene ticket_type cargado, verificar directamente
            if (isset($item['ticket_type']) && isset($item['ticket_type']->event)) {
                if ($item['ticket_type']->event->spaces_id == $spaceId) {
                    $filteredCart[$ticketTypeId] = $item;
                }
            } else {
                // Si no está cargado, cargar la relación y verificar
                $ticketType = TicketType::with('event')->find($ticketTypeId);
                if ($ticketType && $ticketType->event && $ticketType->event->spaces_id == $spaceId) {
                    $item['ticket_type'] = $ticketType;
                    $filteredCart[$ticketTypeId] = $item;
                }
            }
        }
        
        return $filteredCart;
    }
    
    /**
     * Obtener el conteo del carrito filtrado
     */
    public static function getCartCount($host = null)
    {
        $cart = self::getFilteredCart($host);
        return count($cart);
    }
    
    /**
     * Obtener el total del carrito filtrado
     */
    public static function getCartTotal($host = null)
    {
        $cart = self::getFilteredCart($host);
        $total = 0;
        
        foreach ($cart as $item) {
            $total += $item['price'] * $item['quantity'];
        }
        
        return $total;
    }
    
    /**
     * Obtener la ruta correcta para agregar al carrito
     */
    public static function getCartAddRoute($host = null)
    {
        $host = $host ?: request()->getHost();
        $isSubdomain = self::isSubdomain($host);
        
        if ($isSubdomain) {
            return route('subdomain.cart.add');
        }
        
        return route('cart.add');
    }
    
    /**
     * Obtener la ruta correcta para ver el carrito
     */
    public static function getCartViewRoute($host = null)
    {
        $host = $host ?: request()->getHost();
        $isSubdomain = self::isSubdomain($host);
        
        if ($isSubdomain) {
            return route('subdomain.cart');
        }
        
        return route('cart');
    }
    
    /**
     * Obtener la ruta correcta para checkout
     */
    public static function getCheckoutRoute($host = null)
    {
        $host = $host ?: request()->getHost();
        $isSubdomain = self::isSubdomain($host);
        
        if ($isSubdomain) {
            return route('subdomain.checkout.checkout');
        }
        
        return route('checkout.checkout');
    }
}
