<?php

namespace App\Helpers;

use App\Models\Space;
use App\Models\TicketType;

class CartHelper
{
    /**
     * Obtener el carrito (global, sin filtrado)
     */
    public static function getFilteredCart($host = null)
    {
        // El carrito es global - retornar siempre el carrito completo
        return session()->get('cart', []);
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
     * Obtener el subdominio del host
     */
    public static function getSubdomain($host)
    {
        $parts = explode('.', $host);
        if (count($parts) > 2) {
            return $parts[0]; // Primer elemento es el subdominio
        }
        return null;
    }

    /**
     * Filtrar carrito para mostrar solo boletos del espacio actual
     */
    public static function filterCartBySpace($cart, $spaceId)
    {
        $filteredCart = [];

        foreach ($cart as $key => $item) {
            // Si el item tiene event_id, verificar directamente
            if (isset($item['event_id'])) {
                $event = \App\Models\Event::find($item['event_id']);
                if ($event && $event->spaces_id == $spaceId) {
                    $filteredCart[$key] = $item;
                }
            } else {
                // Si no está cargado, cargar la relación y verificar
                $ticketType = TicketType::with('events')->find($item['ticket_type_id'] ?? $key);
                if ($ticketType && $ticketType->events) {
                    foreach ($ticketType->events as $event) {
                        if ($event->spaces_id == $spaceId) {
                            $item['event_id'] = $event->id;
                            $item['event'] = $event;
                            $filteredCart[$key] = $item;
                            break;
                        }
                    }
                }
            }
        }

        return $filteredCart;
    }

    /**
     * Obtener el conteo del carrito (global) - suma de cantidades
     */
    public static function getCartCount($host = null)
    {
        $cart = session()->get('cart', []);
        $count = 0;
        
        foreach ($cart as $item) {
            $count += isset($item['quantity']) ? (int)$item['quantity'] : 0;
        }
        
        return $count;
    }

    /**
     * Obtener el total del carrito (global)
     */
    public static function getCartTotal($host = null)
    {
        $cart = session()->get('cart', []);
        $total = 0;

        foreach ($cart as $item) {
            $total += $item['price'] * $item['quantity'];
        }

        return $total;
    }

    /**
     * Obtener la ruta correcta para agregar al carrito (siempre dominio base)
     */
    public static function getCartAddRoute($host = null)
    {
        return config('app.url') . '/cart/add';
    }
    
    /**
     * Obtener la ruta correcta para obtener el conteo del carrito (siempre dominio base)
     */
    public static function getCartCountRoute($host = null)
    {
        return config('app.url') . '/cart/count';
    }
    
    /**
     * Obtener la ruta correcta para obtener el dropdown del carrito (siempre dominio base)
     */
    public static function getCartDropdownRoute($host = null)
    {
        return config('app.url') . '/cart/dropdown';
    }

    /**
     * Obtener la ruta correcta para ver el carrito (siempre dominio base)
     */
    public static function getCartViewRoute($host = null)
    {
        return config('app.url') . '/cart';
    }

    /**
     * Obtener la ruta correcta para checkout (siempre dominio base)
     */
    public static function getCheckoutRoute($host = null)
    {
        return config('app.url') . '/checkout/checkout';
    }

    /**
     * Obtener el carrito con información completa del evento
     */
    public static function getCartWithEventInfo($host = null)
    {
        $cart = self::getFilteredCart($host);
        $cartWithInfo = [];

        foreach ($cart as $key => $item) {
            // Validar que el item tenga los campos mínimos necesarios
            if (!isset($item['quantity']) || !isset($item['price'])) {
                // Saltar items inválidos
                continue;
            }

            // Si tiene event_id y ticket_type_id, obtener el precio actualizado desde TicketsEvent
            if (isset($item['event_id']) && isset($item['ticket_type_id'])) {
                try {
                    $ticketEvent = \App\Models\TicketsEvent::where('ticket_types_id', $item['ticket_type_id'])
                        ->where('event_id', $item['event_id'])
                        ->with(['event', 'ticket_type'])
                        ->first();

                    if ($ticketEvent && $ticketEvent->event && $ticketEvent->ticket_type) {
                        // Actualizar precio desde la BD para asegurar que sea el correcto
                        $item['price'] = $ticketEvent->price;
                        
                        // Actualizar información del evento si no está presente
                        if (!isset($item['event_name'])) {
                            $item['event'] = $ticketEvent->event;
                            $item['event_name'] = $ticketEvent->event->name;
                            $item['event_date'] = $ticketEvent->event->date;
                            $item['event_image'] = $ticketEvent->event->image;
                        }
                        
                        // Actualizar información del tipo de boleto si no está presente
                        if (!isset($item['ticket_type_name'])) {
                            $item['ticket_type_name'] = $ticketEvent->ticket_type->name;
                        }
                    }
                } catch (\Exception $e) {
                    // Si hay un error, mantener el item con los datos que tiene
                    \Log::warning('Error al obtener información del ticket event: ' . $e->getMessage());
                }
            } elseif (!isset($item['event_id']) && isset($item['ticket_type_id'])) {
                // Si no tiene event_id, buscar el evento asociado al tipo de boleto
                try {
                    $ticketEvent = \App\Models\TicketsEvent::where('ticket_types_id', $item['ticket_type_id'])
                        ->with(['event', 'ticket_type'])
                        ->first();

                    if ($ticketEvent && $ticketEvent->event && $ticketEvent->ticket_type) {
                        $item['event_id'] = $ticketEvent->event_id;
                        $item['event'] = $ticketEvent->event;
                        $item['event_name'] = $ticketEvent->event->name;
                        $item['event_date'] = $ticketEvent->event->date;
                        $item['event_image'] = $ticketEvent->event->image;
                        // Actualizar precio desde la BD
                        $item['price'] = $ticketEvent->price;
                        if (!isset($item['ticket_type_name'])) {
                            $item['ticket_type_name'] = $ticketEvent->ticket_type->name;
                        }
                    }
                } catch (\Exception $e) {
                    // Si hay un error, mantener el item con los datos que tiene
                    \Log::warning('Error al buscar evento para ticket type: ' . $e->getMessage());
                }
            }

            // Asegurar que los campos requeridos estén presentes
            if (!isset($item['ticket_type_name'])) {
                $item['ticket_type_name'] = 'Tipo de Boleto';
            }
            if (!isset($item['event_name'])) {
                $item['event_name'] = 'Evento';
            }

            $cartWithInfo[$key] = $item;
        }

        return $cartWithInfo;
    }

    /**
     * Crear reserva temporal para un item del carrito
     */
    public static function createReservation($ticketTypeId, $eventId, $quantity, $minutes = 15)
    {
        $sessionId = session()->getId();

        // Verificar si ya existe una reserva activa
        $existingReservation = \App\Models\TicketReservation::where('session_id', $sessionId)
            ->where('ticket_types_id', $ticketTypeId)
            ->where('event_id', $eventId)
            ->where('reserved_until', '>', now())
            ->where('is_active', true)
            ->first();

        if ($existingReservation) {
            $existingReservation->update([
                'quantity' => $quantity,
                'reserved_until' => now()->addMinutes($minutes)
            ]);
            return $existingReservation;
        }

        return \App\Models\TicketReservation::createReservation(
            $sessionId,
            $ticketTypeId,
            $eventId,
            $quantity,
            $minutes
        );
    }

    /**
     * Obtener reservas activas del usuario actual
     */
    public static function getActiveReservations()
    {
        $sessionId = session()->getId();
        return \App\Models\TicketReservation::getActiveReservations($sessionId);
    }

    /**
     * Limpiar reservas expiradas
     */
    public static function cleanExpiredReservations()
    {
        \App\Models\TicketReservation::where('reserved_until', '<', now())->delete();
    }
}
