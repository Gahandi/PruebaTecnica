<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use App\Models\Checkin;
use Illuminate\Http\Request;
use Carbon\Carbon;

class CheckinController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:view checkins')->only(['index', 'show']);
        $this->middleware('permission:create checkins')->only(['store']);
        $this->middleware('permission:edit checkins')->only(['update']);
        $this->middleware('permission:delete checkins')->only(['destroy']);
    }

    /**
     * Muestra la interfaz para escanear QR y una lista de los check-ins recientes.
     */
    public function index(Request $request)
    {
        // Si es staff y no admin, redirigir a la ruta de staff
        if (auth()->user()->hasRole('staff') && !auth()->user()->hasRole('admin')) {
            return redirect()->route('staff.checkins.index');
        }
        
        $query = Checkin::with(['ticket.order.user', 'ticket.ticketType.event']);
        
        // Filtrar por evento si se especifica
        if ($request->has('event_id') && $request->event_id) {
            $query->whereHas('ticket.ticketType', function($q) use ($request) {
                $q->where('event_id', $request->event_id);
            });
        }
        
        $checkins = $query->latest()->paginate(20);
        $events = \App\Models\Event::all();
        
        return view('admin.checkins.index', compact('checkins', 'events'));
    }

    /**
     * Procesa el escaneo de un boleto.
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'qr_code' => 'required|string',
            ]);

            $qrCode = $request->qr_code;
            \Log::info('Intento de check-in con código QR: ' . $qrCode);
            
            $ticket = null;
            
            // Si es una URL, extraer el ID del ticket
            if (strpos($qrCode, '/checkin/') !== false) {
                // Extraer el ID del ticket de la URL
                $ticketId = basename(parse_url($qrCode, PHP_URL_PATH));
                \Log::info('ID de boleto extraído de la URL: ' . $ticketId);
                $ticket = Ticket::find($ticketId);
            } else {
                // Primero intentar buscar por ID del ticket (UUID)
                if (preg_match('/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/i', $qrCode)) {
                    \Log::info('Buscando por ID de boleto: ' . $qrCode);
                    $ticket = Ticket::find($qrCode);
                }
                
                // Si no se encontró por ID, buscar por qr_code
                if (!$ticket) {
                    \Log::info('Buscando por qr_code: ' . $qrCode);
                    $ticket = Ticket::where('qr_code', $qrCode)->first();
                }
            }

            if (!$ticket) {
                \Log::info('Boleto no encontrado para el código QR: ' . $qrCode);
                return response()->json([
                    'success' => false,
                    'message' => 'Boleto no encontrado. Verifica el código QR.',
                ], 404);
            }

            // Cargar relaciones necesarias
            $ticket->load(['ticketType.event', 'order.user', 'checkin']);

            // Verificar si el boleto ya fue usado
            if ($ticket->checkin) {
                \Log::info('Boleto ya utilizado: ' . $ticket->id);
                return response()->json([
                    'success' => false,
                    'message' => 'Este boleto ya ha sido utilizado.',
                    'checkin_at' => $ticket->checkin->scanned_at->format('d/m/Y H:i:s'),
                    'ticket_info' => [
                        'id' => $ticket->id,
                        'event' => $ticket->ticketType->event->name,
                        'ticket_type' => $ticket->ticketType->name,
                        'customer' => $ticket->order->user->name,
                    ]
                ], 409);
            }

            // Crear el check-in
            $checkin = Checkin::create([
                'ticket_id' => $ticket->id,
                'scanned_at' => Carbon::now(),
                'scanned_by' => auth()->id(),
            ]);

            \Log::info('Check-in exitoso para el boleto: ' . $ticket->id);

            return response()->json([
                'success' => true,
                'message' => 'Check-in exitoso.',
                'ticket_info' => [
                    'id' => $ticket->id,
                    'event' => $ticket->ticketType->event->name,
                    'ticket_type' => $ticket->ticketType->name,
                    'customer' => $ticket->order->user->name,
                    'checked_in_at' => $checkin->scanned_at->format('d/m/Y H:i:s'),
                ]
            ], 200);

        } catch (\Exception $e) {
            \Log::error('Error en check-in: ' . $e->getMessage());
            \Log::error('Traza del error: ' . $e->getTraceAsString());
            
            return response()->json([
                'success' => false,
                'message' => 'Error interno del servidor. Inténtalo de nuevo.',
            ], 500);
        }
    }

    /**
     * Mostrar detalles de un check-in específico
     */
    public function show(Checkin $checkin)
    {
        // Si es staff y no admin, redirigir a la ruta de staff
        if (auth()->user()->hasRole('staff') && !auth()->user()->hasRole('admin')) {
            return redirect()->route('staff.checkins.show', $checkin);
        }
        
        $checkin->load(['ticket.order.user', 'ticket.ticketType.event']);
        return view('admin.checkins.show', compact('checkin'));
    }

    /**
     * Eliminar un check-in (solo para administradores)
     */
    public function destroy(Checkin $checkin)
    {
        $checkin->delete();
        
        return redirect()->route('admin.checkins.index')
                         ->with('success', 'Check-in eliminado correctamente.');
    }
}
