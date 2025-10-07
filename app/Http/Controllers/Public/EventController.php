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
        $events = Event::with('ticketTypes')->get();

        return view('public.events.index', compact('events'));
    }

    public function show(Event $event)
    {
        $event->load('ticketTypes');

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
        $cart = session()->get('cart', []);
        Log::info($cart);

        // Verificar disponibilidad
        $currentQuantity = isset($cart[$ticket_event->ticket_type_id]) ? $cart[$ticket_event->ticket_type_id]['quantity'] : 0;
        $totalQuantity = $currentQuantity + $request->quantity;

        if ($totalQuantity > $ticket_event->quantity) {
            return back()->with('error', 'No hay suficientes boletos disponibles.');
        }

        if (isset($cart[$ticket_event->ticket_type_id])) {
            $cart[$ticket_event->ticket_type_id]['quantity'] += $request->quantity;
        } else {
            $cart[$ticket_event->ticket_type_id] = [
                'ticket_type' => $ticket_event->ticket_type_id,
                'quantity' => $request->quantity,
                'price' => $ticket_event->price,
            ];
        }

        session()->put('cart', $cart);

        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Boletos agregados al carrito.',
                'cart_count' => count($cart),
                'cart' => json_encode($cart)
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
