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
        $search = $request->get('search', '') ?? '';
        $tagId = $request->get('tag', null) ?? null;
        $categoryId = $request->get('category', null) ?? null;

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
        $categories = TypeEvent::withCount([
            'events as events_count' => function ($query) {
                $query->whereDate('date', '>=', now()); 
            }
        ])        
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
        try {
            $tags = Tag::withCount('events')
                ->having('events_count', '>', 0)
                ->orderBy('events_count', 'desc')
                ->limit(20)
                ->get();
        } catch (\Exception $e) {
            $tags = collect([]);
        }

        // Asegurar que tags siempre sea una colección
        if (!isset($tags) || !$tags) {
            $tags = collect([]);
        }

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
        $minPrice = $request->get('min_price', null);
        $maxPrice = $request->get('max_price', null);
        $sortBy = $request->get('sort', 'date_asc'); // date_asc, date_desc, price_asc, price_desc

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

        // Aplicar filtro por precio
        if ($minPrice !== null || $maxPrice !== null) {
            $eventsQuery->whereHas('ticketTypes', function($query) use ($minPrice, $maxPrice) {
                if ($minPrice !== null) {
                    $query->where('tickets_events.price', '>=', $minPrice);
                }
                if ($maxPrice !== null) {
                    $query->where('tickets_events.price', '<=', $maxPrice);
                }
            });
        }

        // Aplicar ordenamiento
        switch ($sortBy) {
            case 'date_desc':
                $eventsQuery->orderBy('date', 'desc');
                break;
            case 'price_asc':
                // Ordenar por precio mínimo usando subquery para evitar problemas con paginación
                $eventsQuery->select('events.*')
                    ->selectRaw('(SELECT MIN(price) FROM tickets_events WHERE tickets_events.event_id = events.id) as min_price')
                    ->orderBy('min_price', 'asc')
                    ->orderBy('date', 'asc');
                break;
            case 'price_desc':
                $eventsQuery->select('events.*')
                    ->selectRaw('(SELECT MIN(price) FROM tickets_events WHERE tickets_events.event_id = events.id) as min_price')
                    ->orderBy('min_price', 'desc')
                    ->orderBy('date', 'asc');
                break;
            case 'date_asc':
            default:
                $eventsQuery->orderBy('date', 'asc');
                break;
        }

        // Obtener eventos
        $events = $eventsQuery
            ->paginate(12);

        // Cargar tags en todos los eventos
        $events->load('tags');

        // Obtener categorías con conteo de eventos
        $categories = TypeEvent::withCount([
            'events as events_count' => function ($query) {
                $query->whereDate('date', '>=', now());
            }
        ])
        
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
        try {
            $tags = Tag::withCount('events')
                ->having('events_count', '>', 0)
                ->orderBy('events_count', 'desc')
                ->limit(50)
                ->get();
        } catch (\Exception $e) {
            $tags = collect([]);
        }

        // Calcular rangos de precio
        $priceRanges = [
            ['min' => 0, 'max' => 500, 'label' => 'Menos de $500'],
            ['min' => 500, 'max' => 1000, 'label' => '$500 - $1,000'],
            ['min' => 1000, 'max' => 2000, 'label' => '$1,000 - $2,000'],
            ['min' => 2000, 'max' => 5000, 'label' => '$2,000 - $5,000'],
            ['min' => 5000, 'max' => null, 'label' => 'Más de $5,000'],
        ];

        return view('events.search', compact('events', 'categories', 'tags', 'search', 'tagId', 'categoryId', 'minPrice', 'maxPrice', 'sortBy', 'priceRanges'));
    }
}
