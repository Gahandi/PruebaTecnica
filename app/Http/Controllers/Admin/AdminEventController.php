<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Event;
use App\Models\Space;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Ticket;

class AdminEventController extends Controller
{
    /**
     * Lista de eventos agrupados por espacio
     */
    public function index()
    {
        $spaces = Space::withCount('events')
            ->with([
                'events' => function ($query) {
                    $query->withCount(['tickets_events', 'orders'])
                        ->with(['type_event'])
                        ->latest()
                        ->take(10);
                }
            ])
            ->orderBy('name')
            ->get();

        $totalEvents = Event::count();
        $totalSpaces = $spaces->count();

        return view('admin.events.index', compact('spaces', 'totalEvents', 'totalSpaces'));
    }

    /**
     * Mostrar detalles completos de un evento
     */
    public function show(Event $event)
    {
        $event->load([
            'space',
            'type_event',
            'state',
            'tickets_events.ticket_type',
        ]);

        // Estadísticas del evento
        $completedOrderIds = $event->orders()->where('status', 'completed')->pluck('id');
        $stats = [
            'total_orders' => $event->orders()->count(),
            'completed_orders' => $completedOrderIds->count(),
            'total_tickets' => $event->tickets()->count(),
            'checked_in' => $event->tickets()->where('used', true)->count(),
            'total_revenue' => Payment::whereIn('order_id', $completedOrderIds)->sum('total'),
        ];

        // Órdenes del evento paginadas
        $orders = Order::where('event_id', $event->id)
            ->with(['user'])
            ->latest()
            ->paginate(15);

        // Tipos de boletos con conteos
        $ticketTypes = $event->tickets_events()
            ->with('ticket_type')
            ->get()
            ->map(function ($te) use ($event) {
                $sold = Ticket::where('ticket_types_id', $te->ticket_types_id)
                    ->where('event_id', $event->id)
                    ->count();
                return [
                    'id' => $te->id,
                    'name' => $te->ticket_type->name ?? 'Sin tipo',
                    'price' => $te->price,
                    'quantity' => $te->quantity,
                    'sold' => $sold,
                    'available' => $te->quantity - $sold,
                ];
            });

        return view('admin.events.show', compact('event', 'stats', 'orders', 'ticketTypes'));
    }

    /**
     * Eliminar evento (Soft Delete)
     */
    public function destroy(Event $event)
    {
        $event->delete();

        return redirect()
            ->route('admin.events.index')
            ->with('success', 'Evento eliminado correctamente.');
    }
}
