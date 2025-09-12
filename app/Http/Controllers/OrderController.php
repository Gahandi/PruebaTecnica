<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Ticket;
use Illuminate\Http\Request;
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
        
        $ticket->load(['order.user', 'ticketType.event', 'checkin']);
        return view('tickets.show', compact('ticket'));
    }

    public function myTickets()
    {
        $userId = auth()->id();
        \Log::info('MyTickets - User ID: ' . $userId);
        
        // Cargar órdenes del usuario con sus boletos agrupados
        $orders = Order::where('user_id', $userId)
            ->with(['tickets.ticketType.event', 'tickets.checkin', 'event', 'coupon'])
            ->latest()
            ->paginate(10);
        
        \Log::info('MyTickets - Orders found: ' . $orders->count());
        
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
