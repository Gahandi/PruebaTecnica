<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\Event;
use App\Models\Checkin;
use Illuminate\Support\Facades\Auth;

class TicketController extends Controller
{
    /**
     * Display the specified ticket.
     */
    public function show(string $id): JsonResponse
    {
        $ticket = Ticket::with(['order.event', 'order.items.ticketType', 'checkin'])
            ->find($id);
        
        if (!$ticket) {
            return response()->json([
                'success' => false,
                'message' => 'Ticket not found'
            ], 404);
        }
        
        return response()->json([
            'success' => true,
            'data' => $ticket
        ]);
    }

    /**
     * Validate ticket for check-in.
     */
    public function validateTicket(string $id): JsonResponse
    {
        $ticket = Ticket::with(['order.event', 'checkin'])->find($id);
        
        if (!$ticket) {
            return response()->json([
                'success' => false,
                'message' => 'Ticket not found'
            ], 404);
        }
        $event = Event::find($ticket->event_id);
        
        if ($ticket->used) {
            return response()->json([
                'success' => false,
                'message' => 'Ticket already used',
                'data' => [
                    'event'   => $event,
                    'ticket'  => $ticket,
                    'used_at' => $ticket->updated_at,
                ]
            ], 400);
        }

        //Marcar como usado
        $ticket->update(['used' => true]);

        Checkin::create([
            'ticket_id'  => $ticket->id,
            'scanned_at' => now(),
            'scanned_by' => Auth::id() ?? null, 
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Ticket valid',
            'data' => [
                'ticket' => $ticket,
                'event' => $event,
                'used_at' => now()->toDateTimeString(),
            ]
        ]);
    }
}
