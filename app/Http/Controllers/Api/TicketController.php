<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\Event;
use App\Models\Checkin;
use App\Models\SpacesUser;
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
                'message' => 'Boleto no encontrado'
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
        // 1. Buscar el ticket por ID
        $ticket = Ticket::with(['checkin'])->find($id);

        if (!$ticket) {
            return response()->json([
                'success' => false,
                'message' => 'Boleto no encontrado'
            ], 404);
        }

        // 2. Obtener el event_id del ticket
        $eventId = $ticket->event_id;
        
        if (!$eventId) {
            return response()->json([
                'success' => false,
                'message' => 'El boleto no tiene un evento asociado'
            ], 400);
        }

        // 3. Buscar el evento y su space
        $event = Event::with('space')->find($eventId);
        
        if (!$event) {
            return response()->json([
                'success' => false,
                'message' => 'Evento no encontrado'
            ], 404);
        }

        $spaceId = $event->spaces_id;
        $user = auth()->user();

        // Debug: verificar autenticación
        \Log::info('Validando ticket - Usuario autenticado:', [
            'user_id' => $user?->id,
            'is_authenticated' => auth()->check(),
            'session_id' => session()->getId(),
            'space_id' => $spaceId
        ]);

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Usuario no autenticado',
                'debug' => [
                    'auth_check' => auth()->check(),
                    'session_id' => session()->getId(),
                ]
            ], 401);
        }

        // 4. Buscar en spaces_users si el usuario tiene un rol diferente de viewer en ese espacio
        $spaceUser = SpacesUser::where('user_id', $user->id)
            ->where('space_id', $spaceId)
            ->whereNull('deleted_at')
            ->with('role_space')
            ->first();

        if (!$spaceUser) {
            return response()->json([
                'success' => false,
                'message' => 'No tienes acceso a este espacio',
                'data' => [
                    'ticket_id' => $ticket->id,
                    'event_id' => $eventId,
                    'space_id' => $spaceId,
                    'space_name' => $event->space->name ?? 'N/A',
                ]
            ], 403);
        }

        // 5. Verificar que el rol sea diferente de 'viewer'
        $roleName = $spaceUser->role_space->name ?? null;
        
        if ($roleName === 'viewer') {
            return response()->json([
                'success' => false,
                'message' => 'No tienes permisos para escanear boletos. Solo los roles admin y staff pueden escanear.',
                'data' => [
                    'ticket_id' => $ticket->id,
                    'event_id' => $eventId,
                    'space_id' => $spaceId,
                    'space_name' => $event->space->name ?? 'N/A',
                    'your_role' => $roleName,
                ]
            ], 403);
        }

        if ($ticket->used) {
            return response()->json([
                'success' => false,
                'message' => 'Este boleto ya ha sido utilizado',
                'data' => [
                    'event'   => $event,
                    'ticket'  => $ticket,
                    'used_at' => $ticket->updated_at,
                ]
            ], 400);
        }

        // ✔ Marcar como usado
        $ticket->update(['used' => true]);

        Checkin::create([
            'ticket_id'  => $ticket->id,
            'scanned_at' => now(),
            'scanned_by' => $user->id,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Boleto válido',
            'data' => [
                'ticket' => $ticket,
                'event' => $event,
                'used_at' => now()->toDateTimeString(),
            ]
        ]);
    }

}
