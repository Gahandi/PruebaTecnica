<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class TicketReservation extends Model
{
    use HasFactory;

    protected $fillable = [
        'session_id',
        'ticket_types_id',
        'event_id',
        'quantity',
        'reserved_until',
        'is_active'
    ];

    protected $casts = [
        'reserved_until' => 'datetime',
        'quantity' => 'integer',
        'is_active' => 'boolean'
    ];

    public function ticket_type()
    {
        return $this->belongsTo(TicketType::class, 'ticket_types_id');
    }

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function tickets_event()
    {
        return $this->belongsTo(TicketsEvent::class, 'ticket_types_id', 'ticket_types_id')
            ->where('event_id', $this->event_id);
    }

    public function isExpired()
    {
        return $this->reserved_until < now();
    }

    public static function createReservation($sessionId, $ticketTypeId, $eventId, $quantity, $minutes = 15)
    {
        // Eliminar reservas expiradas del mismo usuario
        self::where('session_id', $sessionId)
            ->where('reserved_until', '<', now())
            ->delete();

        // Crear nueva reserva
        return self::create([
            'session_id' => $sessionId,
            'ticket_types_id' => $ticketTypeId,
            'event_id' => $eventId,
            'quantity' => $quantity,
            'reserved_until' => now()->addMinutes($minutes),
            'is_active' => true
        ]);
    }

    public static function getActiveReservations($sessionId)
    {
        return self::where('session_id', $sessionId)
            ->where('reserved_until', '>', now())
            ->where('is_active', true)
            ->with(['ticket_type', 'event', 'tickets_event'])
            ->get();
    }
}
