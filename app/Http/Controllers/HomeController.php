<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\TypeEvent;
use Illuminate\Http\Request;

class HomeController extends Controller
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
}
