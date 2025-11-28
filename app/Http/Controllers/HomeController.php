<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Tag;
use App\Models\TypeEvent;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        // Obtener parámetros de búsqueda y filtrado
        $search = $request->get('search', '');
        $tagId = $request->get('tag', null);
        $categoryId = $request->get('category', null);

        // Query base para eventos
        $eventsQuery = Event::with(['space', 'ticketTypes', 'tags', 'type_event'])
            ->where('active', true)
            ->where('date', '>=', now());

        // Aplicar búsqueda por texto
        if (!empty($search)) {
            $eventsQuery->where(function($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('address', 'like', "%{$search}%")
                    ->orWhereHas('space', function($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%")
                          ->orWhere('keywords', 'like', "%{$search}%");
                    });
            });
        }

        // Aplicar filtro por tag
        if ($tagId) {
            $eventsQuery->whereHas('tags', function($query) use ($tagId) {
                $query->where('tags.id', $tagId);
            });
        }

        // Aplicar filtro por categoría
        if ($categoryId) {
            $eventsQuery->where('type_events_id', $categoryId);
        }

        // Obtener eventos destacados
        $featuredEvents = (clone $eventsQuery)
            ->orderBy('date', 'asc')
            ->limit(6)
            ->get();

        // Obtener todos los eventos para la sección principal
        $allEvents = $eventsQuery
            ->orderBy('date', 'asc')
            ->limit(12)
            ->get();

        // Cargar tags en todos los eventos
        $featuredEvents->load('tags');
        $allEvents->load('tags');

        // Obtener categorías con conteo de eventos
        $categories = TypeEvent::withCount('events')
            ->having('events_count', '>', 0)
            ->get()
            ->map(function ($type) {
                return [
                    'id' => $type->id,
                    'name' => $type->name,
                    'count' => $type->events_count
                ];
            });

        // Obtener todos los tags con conteo de eventos
        $tags = Tag::withCount('events')
            ->having('events_count', '>', 0)
            ->orderBy('events_count', 'desc')
            ->limit(20)
            ->get();

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

        return view('home', compact('featuredEvents', 'allEvents', 'categories', 'tags', 'search', 'tagId', 'categoryId'));
    }

    public function search(Request $request)
    {
        // Obtener parámetros de búsqueda y filtrado
        $search = $request->get('q', '');
        $tagId = $request->get('tag', null);
        $categoryId = $request->get('category', null);

        // Query base para eventos
        $eventsQuery = Event::with(['space', 'ticketTypes', 'tags', 'type_event'])
            ->where('active', true)
            ->where('date', '>=', now());

        // Aplicar búsqueda por texto
        if (!empty($search)) {
            $eventsQuery->where(function($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('address', 'like', "%{$search}%")
                    ->orWhereHas('space', function($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%")
                          ->orWhere('keywords', 'like', "%{$search}%");
                    })
                    ->orWhereHas('tags', function($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%");
                    });
            });
        }

        // Aplicar filtro por tag
        if ($tagId) {
            $eventsQuery->whereHas('tags', function($query) use ($tagId) {
                $query->where('tags.id', $tagId);
            });
        }

        // Aplicar filtro por categoría
        if ($categoryId) {
            $eventsQuery->where('type_events_id', $categoryId);
        }

        // Obtener eventos
        $events = $eventsQuery
            ->orderBy('date', 'asc')
            ->paginate(12);

        // Cargar tags en todos los eventos
        $events->load('tags');

        // Obtener categorías con conteo de eventos
        $categories = TypeEvent::withCount('events')
            ->having('events_count', '>', 0)
            ->get()
            ->map(function ($type) {
                return [
                    'id' => $type->id,
                    'name' => $type->name,
                    'count' => $type->events_count
                ];
            });

        // Obtener todos los tags con conteo de eventos
        $tags = Tag::withCount('events')
            ->having('events_count', '>', 0)
            ->orderBy('events_count', 'desc')
            ->limit(20)
            ->get();

        return view('events.search', compact('events', 'categories', 'tags', 'search', 'tagId', 'categoryId'));
    }
}
