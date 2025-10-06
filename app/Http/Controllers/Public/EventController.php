<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\TicketType;
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

        $ticketType = TicketType::with(['event.space'])->findOrFail($request->ticket_type_id);
        $cart = session()->get('cart', []);
        
        // Verificar disponibilidad
        $currentQuantity = isset($cart[$ticketType->id]) ? $cart[$ticketType->id]['quantity'] : 0;
        $totalQuantity = $currentQuantity + $request->quantity;
        
        if ($totalQuantity > $ticketType->quantity) {
            return back()->with('error', 'No hay suficientes boletos disponibles.');
        }

        if (isset($cart[$ticketType->id])) {
            $cart[$ticketType->id]['quantity'] += $request->quantity;
        } else {
            $cart[$ticketType->id] = [
                'ticket_type' => $ticketType,
                'quantity' => $request->quantity,
                'price' => $ticketType->price,
            ];
        }

        session()->put('cart', $cart);
        
        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Boletos agregados al carrito.',
                'cart_count' => count($cart)
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
