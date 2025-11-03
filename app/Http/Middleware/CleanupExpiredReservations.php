<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\TicketReservation;

class CleanupExpiredReservations
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Limpiar reservas expiradas cada 10% de las veces (para no sobrecargar)
        if (rand(1, 10) === 1) {
            TicketReservation::where('reserved_until', '<', now())->delete();
        }
        
        return $next($request);
    }
}
