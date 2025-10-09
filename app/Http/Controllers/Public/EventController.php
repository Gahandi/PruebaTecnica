<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\TicketType;
use App\Models\TicketsEvent;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;

class EventController extends Controller
{
    public function index()
    {
        $events = Event::with(['ticketTypes' => function($query) {
            $query->withPivot('quantity', 'price');
        }])->get();

        return view('public.events.index', compact('events'));
    }

    public function show(Event $event)
    {
        $event->load(['ticketTypes' => function($query) {
            $query->withPivot('quantity', 'price');
        }]);

        return view('public.events.show', compact('event'));
    }

    /**
     * Agregar boletos al carrito (sin autenticación)
     */
    public function addToCart(Request $request)
    {
        $request->validate([
            'ticket_type_id' => 'required|exists:ticket_types,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $ticket_event = TicketsEvent::where('ticket_types_id', $request->ticket_type_id)->first();

        if (!$ticket_event) {
            return back()->with('error', 'Tipo de boleto no encontrado.');
        }

        $cart = session()->get('cart', []);
        $cartKey = $ticket_event->ticket_types_id . '_' . $ticket_event->event_id;

        // Verificar disponibilidad considerando reservas activas
        $currentQuantity = isset($cart[$cartKey]) ? $cart[$cartKey]['quantity'] : 0;
        $totalQuantity = $currentQuantity + $request->quantity;

        // Verificar reservas activas de otros usuarios (excluyendo la sesión actual)
        $reservedByOthers = \App\Models\TicketReservation::where('ticket_types_id', $ticket_event->ticket_types_id)
            ->where('event_id', $ticket_event->event_id)
            ->where('reserved_until', '>', now())
            ->where('is_active', true)
            ->where('session_id', '!=', session()->getId())
            ->sum('quantity');

        // Verificar reservas del usuario actual
        $reservedByCurrentUser = \App\Models\TicketReservation::where('ticket_types_id', $ticket_event->ticket_types_id)
            ->where('event_id', $ticket_event->event_id)
            ->where('reserved_until', '>', now())
            ->where('is_active', true)
            ->where('session_id', session()->getId())
            ->sum('quantity');

        // Calcular disponibilidad real
        $availableQuantity = $ticket_event->quantity - $reservedByOthers - $reservedByCurrentUser;

        if ($totalQuantity > $availableQuantity) {
            return back()->with('error', 'No hay suficientes boletos disponibles. Solo quedan ' . $availableQuantity . ' boletos.');
        }

        // Crear reserva temporal
        \App\Helpers\CartHelper::createReservation(
            $ticket_event->ticket_types_id,
            $ticket_event->event_id,
            $totalQuantity,
            15 // 15 minutos
        );

        if (isset($cart[$cartKey])) {
            $cart[$cartKey]['quantity'] = $totalQuantity;
        } else {
            $cart[$cartKey] = [
                'ticket_type_id' => $ticket_event->ticket_types_id,
                'event_id' => $ticket_event->event_id,
                'quantity' => $request->quantity,
                'price' => $ticket_event->price,
                'ticket_type_name' => $ticket_event->ticket_type->name,
                'event_name' => $ticket_event->event->name,
                'event_date' => $ticket_event->event->date,
                'event_image' => $ticket_event->event->image,
            ];
        }

        session()->put('cart', $cart);

        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Boletos agregados al carrito.',
                'cart_count' => count($cart),
                'cart' => $cart
            ]);
        }

        return back()->with('success', 'Boletos agregados al carrito.');
    }

    /**
     * Proceder al checkout (requiere autenticación)
     */
    public function checkout()
    {
        $cart = session()->get('cart', []);

        if (empty($cart)) {
            return redirect()->route('public.events.index')->with('error', 'Tu carrito está vacío.');
        }

        if (!auth()->check()) {
            return redirect()->route('login')->with('message', 'Por favor inicia sesión para proceder al pago.');
        }

        return redirect()->route('checkout.cart');
    }
}
