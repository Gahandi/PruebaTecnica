<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\TicketType;
use App\Models\TicketsEvent;
use App\Models\Space;
use App\Models\TypeEvent;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;

class EventController extends Controller
{
    public function index()
    {
        // Obtener eventos destacados (próximos eventos)
        $featuredEvents = Event::with(['space', 'ticketTypes'])
            ->where('active', true)
            ->where('date', '>=', now())
            ->orderBy('date', 'asc')
            ->limit(6)
            ->get();

        // Obtener todos los eventos para la sección principal
        $allEvents = Event::with(['space', 'ticketTypes'])
            ->where('active', true)
            ->where('date', '>=', now())
            ->orderBy('date', 'asc')
            ->limit(12)
            ->get();

        // Obtener categorías con conteo de eventos
        $categories = TypeEvent::withCount('events')
            ->having('events_count', '>', 0)
            ->get()
            ->map(function ($type) {
                return [
                    'id' => $type->id,
                    'name' => $type->name,
                    'count' => $type->events_count
                ];
            });

        // Si no hay categorías, crear algunas por defecto
        if ($categories->isEmpty()) {
            $categories = collect([
                ['name' => 'Conciertos', 'count' => 0],
                ['name' => 'Deportes', 'count' => 0],
                ['name' => 'Teatro', 'count' => 0],
                ['name' => 'Comedia', 'count' => 0],
                ['name' => 'Conferencias', 'count' => 0],
                ['name' => 'Festivales', 'count' => 0],
                ['name' => 'Exposiciones', 'count' => 0],
                ['name' => 'Otros', 'count' => 0],
            ]);
        }

        return view('home', compact('featuredEvents', 'allEvents', 'categories'));
    }

    public function show(Request $request, Event $event)
    {
        // Cargar el espacio del evento
        $space = $event->space;

        if (!$space) {
            abort(404, 'Evento no encontrado');
        }

        // Cargar la relación ticketTypes con información de la tabla intermedia
        $event->load(['ticketTypes' => function($query) {
            $query->withPivot('quantity', 'price');
        }]);

        return view('events.show', compact('event', 'space'));
    }

    /**
     * Agregar boletos al carrito (sin autenticación)
     */
    public function addToCart(Request $request)
    {
        // Manejar preflight OPTIONS request
        if ($request->getMethod() === 'OPTIONS') {
            return response('', 200)
                ->header('Access-Control-Allow-Origin', $request->headers->get('Origin'))
                ->header('Access-Control-Allow-Credentials', 'true')
                ->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS')
                ->header('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With, X-CSRF-TOKEN');
        }
        $request->validate([
            'ticket_type_id' => 'required|exists:ticket_types,id',
            'event_id' => 'required|exists:events,id',
            'quantity' => 'required|integer|min:1',
        ]);

        // Buscar el ticket_event específico para este evento y tipo de boleto
        $ticket_event = TicketsEvent::where('ticket_types_id', $request->ticket_type_id)
            ->where('event_id', $request->event_id)
            ->first();

        if (!$ticket_event) {
            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tipo de boleto no encontrado.'
                ], 404);
            }
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
            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No hay suficientes boletos disponibles. Solo quedan ' . $availableQuantity . ' boletos.'
                ], 400);
            }
            return back()->with('error', 'No hay suficientes boletos disponibles. Solo quedan ' . $availableQuantity . ' boletos.');
        }

        // Descontar disponibilidad directamente
        $quantityToDeduct = isset($cart[$cartKey]) ? $request->quantity : $request->quantity;
        
        // Verificar que haya suficiente disponibilidad antes de descontar
        if ($ticket_event->quantity < $quantityToDeduct) {
            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No hay suficientes boletos disponibles. Solo quedan ' . $ticket_event->quantity . ' boletos.'
                ], 400);
            }
            return back()->with('error', 'No hay suficientes boletos disponibles. Solo quedan ' . $ticket_event->quantity . ' boletos.');
        }

        // Descontar de la disponibilidad
        $ticket_event->quantity -= $quantityToDeduct;
        $ticket_event->save();

        // Crear reserva temporal
        \App\Helpers\CartHelper::createReservation(
            $ticket_event->ticket_types_id,
            $ticket_event->event_id,
            $totalQuantity,
            15 // 15 minutos
        );

        if (isset($cart[$cartKey])) {
            $cart[$cartKey]['quantity'] = $totalQuantity;
            // Actualizar precio desde TicketsEvent para asegurar que sea el correcto para este evento
            $cart[$cartKey]['price'] = $ticket_event->price;
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
            // Preparar datos del item para localStorage
            $itemData = [
                'ticket_type_id' => $ticket_event->ticket_types_id,
                'event_id' => $ticket_event->event_id,
                'price' => $ticket_event->price,
                'ticket_type_name' => $ticket_event->ticket_type->name,
                'event_name' => $ticket_event->event->name,
                'event_date' => $ticket_event->event->date,
                'event_image' => $ticket_event->event->image,
            ];

            $origin = $request->headers->get('Origin');
            $response = response()->json([
                'success' => true,
                'message' => 'Boletos agregados al carrito.',
                'cart_count' => \App\Helpers\CartHelper::getCartCount(),
                'cart' => $cart,
                'item_data' => $itemData
            ]);

            // Agregar headers CORS si hay un origin
            if ($origin) {
                $response->header('Access-Control-Allow-Origin', $origin)
                         ->header('Access-Control-Allow-Credentials', 'true')
                         ->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS')
                         ->header('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With, X-CSRF-TOKEN');
            }

            return $response;
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
