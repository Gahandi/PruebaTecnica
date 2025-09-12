<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use App\Models\Checkin;
use Illuminate\Http\Request;
use Carbon\Carbon;

class CheckinController extends Controller
{
    public function __construct()
    {
        // Solo autenticación, sin otros middlewares
        $this->middleware('auth');
    }

    /**
     * Muestra la interfaz para escanear QR y una lista de los check-ins recientes.
     */
    public function index(Request $request)
    {
        $query = Checkin::with(['ticket.order.user', 'ticket.ticketType.event']);
        
        // Filtrar por evento si se especifica
        if ($request->has('event_id') && $request->event_id) {
            $query->whereHas('ticket.ticketType', function($q) use ($request) {
                $q->where('event_id', $request->event_id);
            });
        }
        
        $checkins = $query->latest()->paginate(20);
        $events = \App\Models\Event::all();
        
        return view('staff.checkins.index', compact('checkins', 'events'));
    }

    /**
     * Procesa el escaneo de un boleto.
     */
    public function store(Request $request)
    {
        $request->validate([
            'qr_code' => 'required|string'
        ]);

        // Buscar el ticket por QR code
        $ticket = Ticket::where('qr_code', $request->qr_code)->first();

        if (!$ticket) {
            return response()->json([
                'success' => false,
                'message' => 'Boleto no encontrado. Verifica el código QR.'
            ]);
        }

        // Cargar relaciones necesarias
        $ticket->load(['order.user', 'ticketType.event', 'checkin']);

        // Verificar si el boleto ya fue usado
        if ($ticket->checkin) {
            return response()->json([
                'success' => false,
                'message' => 'Este boleto ya ha sido utilizado.',
                'ticket_info' => [
                    'customer' => $ticket->order->user->name,
                    'event' => $ticket->ticketType->event->name,
                    'ticket_type' => $ticket->ticketType->name,
                ],
                'checkin_at' => $ticket->checkin->scanned_at->format('d/m/Y H:i:s')
            ]);
        }

        // Verificar que el evento no haya pasado
        $eventDate = Carbon::parse($ticket->ticketType->event->date);
        if ($eventDate->isPast()) {
            return response()->json([
                'success' => false,
                'message' => 'Este evento ya ha finalizado.',
                'ticket_info' => [
                    'customer' => $ticket->order->user->name,
                    'event' => $ticket->ticketType->event->name,
                    'ticket_type' => $ticket->ticketType->name,
                ]
            ]);
        }

        // Crear el check-in
        $checkin = Checkin::create([
            'ticket_id' => $ticket->id,
            'scanned_at' => Carbon::now(),
            'scanned_by' => auth()->id(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Check-in realizado exitosamente.',
            'ticket_info' => [
                'customer' => $ticket->order->user->name,
                'event' => $ticket->ticketType->event->name,
                'ticket_type' => $ticket->ticketType->name,
                'checked_in_at' => $checkin->scanned_at->format('d/m/Y H:i:s')
            ]
        ]);
    }

    /**
     * Mostrar detalles de un check-in específico
     */
    public function show(Checkin $checkin)
    {
        $checkin->load(['ticket.order.user', 'ticket.ticketType.event']);
        return view('staff.checkins.show', compact('checkin'));
    }

    /**
     * Eliminar un check-in (solo para administradores)
     */
    public function destroy(Checkin $checkin)
    {
        $checkin->delete();
        
        return redirect()->route('staff.checkins.index')
                         ->with('success', 'Check-in eliminado correctamente.');
    }
}
