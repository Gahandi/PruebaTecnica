<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Ticket;
use App\Models\Payment;
use Illuminate\Http\Request;
use App\Models\Event;
use Barryvdh\DomPDF\Facade\Pdf;

class OrderController extends Controller
{
    public function show(Order $order)
    {
        // Verificar que el usuario puede ver esta orden
        if (auth()->user()->id !== $order->user_id && !auth()->user()->hasRole('admin')) {
            abort(403, 'No tienes permisos para ver esta orden.');
        }

        $order->load(['orderItems.ticketType', 'tickets.ticketType', 'coupon']);
        return view('orders.show', compact('order'));
    }

    public function showTicket(Ticket $ticket)
    {
        // Verificar que el usuario sea el propietario o admin
        if (auth()->id() !== $ticket->order->user_id && !auth()->user()->hasRole('admin')) {
            abort(403, 'No tienes permisos para ver este boleto.');
        }

        $ticket->load(['order.user', 'eventTicket.event.ticketTypes' => function($query) {
            $query->withPivot('quantity', 'price');
        }, 'checkin']);
        return view('tickets.show', compact('ticket'));
    }


    public function myTickets()
    {
        $userId = auth()->id();

        // 1. Obtenemos los pedidos paginados SIN intentar cargar la relación 'event'
        $orders = Order::where('user_id', $userId)
            ->with([
                // 'event', <-- LO QUITAMOS DE AQUÍ
                'tickets.ticketType',
                'tickets.checkin',
                'payments.coupon'
            ])
            ->latest()
            ->paginate(10);

        // 2. Extraemos todos los IDs de eventos de los pedidos que ya tenemos en esta página.
        // ->pluck('event_id') extrae el array, ->flatten() los une todos en una sola lista.
        $eventIds = $orders->pluck('event_id')->flatten()->unique()->filter();

        // 3. Buscamos todos los eventos necesarios en UNA SOLA consulta para ser eficientes.
        // ->keyBy('id') convierte la colección para que podamos buscar eventos por su ID fácilmente.
        $events = Event::whereIn('id', $eventIds)->get()->keyBy('id');

        // 4. Asignamos manualmente cada evento a su orden correspondiente.
        foreach ($orders as $order) {
            // Obtenemos el primer ID del array de event_id de la orden
            $eventId = $order->event_id[0] ?? null;

            // Creamos una propiedad 'event' en el objeto order y le asignamos el evento que encontramos.
            $order->event = $events->get($eventId);
        }

        return view('tickets.my', compact('orders'));
    }

    public function downloadPdf(Ticket $ticket)
    {
        // Verificar que el usuario sea el propietario o admin
        if (auth()->id() !== $ticket->order->user_id && !auth()->user()->hasRole('admin')) {
            abort(403, 'No tienes permisos para descargar este boleto.');
        }

        $ticket->load(['order.user', 'ticketType.event', 'checkin']);

        $pdf = Pdf::loadView('tickets.pdf', compact('ticket'));
        $pdf->setPaper('A4', 'portrait');

        return $pdf->download('boleto-' . substr($ticket->id, 0, 8) . '.pdf');
    }

    /**
     * Procesar check-in de un boleto escaneado
     */
    public function checkinTicket(Ticket $ticket)
    {
        // Si no está autenticado, redirigir a login
        if (!auth()->check()) {
            return redirect()->route('login')->with('error', 'Debes iniciar sesión para realizar check-ins.');
        }

        // Verificar que el usuario tenga permisos de check-in (admin o staff)
        if (!auth()->user()->hasAnyRole(['admin', 'staff'])) {
            return redirect()->route('login')->with('error', 'No tienes permisos para realizar check-ins.');
        }

        $ticket->load(['order.user', 'ticketType.event', 'checkin']);

        // Verificar si el boleto ya fue usado
        if ($ticket->checkin) {
            return view('admin.checkins.result', [
                'success' => false,
                'message' => 'Este boleto ya ha sido utilizado.',
                'ticket' => $ticket,
                'checkin_at' => $ticket->checkin->scanned_at->format('d/m/Y H:i:s'),
            ]);
        }

        // Crear el check-in
        $checkin = \App\Models\Checkin::create([
            'ticket_id' => $ticket->id,
            'scanned_at' => \Carbon\Carbon::now(),
            'scanned_by' => auth()->id(),
        ]);

        return view('admin.checkins.result', [
            'success' => true,
            'message' => 'Check-in exitoso.',
            'ticket' => $ticket,
            'checkin' => $checkin,
        ]);
    }
}
