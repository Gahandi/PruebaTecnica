<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\TypeEvent;
use App\Models\Event;
use Illuminate\Http\Request;
use Carbon\Carbon;

class CategoryController extends Controller
{
    /**
     * Mostrar todas las categorías con paginación
     */
    public function index()
    {
        $categories = TypeEvent::withCount([
            'events' => function ($query) {
                $query->where('date', '>=', Carbon::now())
                    ->where('active', 1);
            }
        ])
            ->orderBy('name')
            ->paginate(20);

        return view('public.categories.index', compact('categories'));
    }

    /**
     * API: Obtener categorías paginadas para scroll infinito
     */
    public function apiIndex()
    {
        $categories = TypeEvent::withCount([
            'events' => function ($query) {
                $query->where('date', '>=', Carbon::now())
                    ->where('active', 1);
            }
        ])
            ->orderBy('name')
            ->paginate(20);

        return response()->json([
            'data' => $categories->items(),
            'current_page' => $categories->currentPage(),
            'last_page' => $categories->lastPage(),
            'has_more' => $categories->hasMorePages(),
            'total' => $categories->total(),
        ]);
    }

    /**
     * Mostrar una categoría específica con sus eventos
     */
    public function show($category)
    {
        $category = TypeEvent::withCount([
            'events' => function ($query) {
                $query->where('date', '>=', Carbon::now())
                    ->where('active', 1);
            }
        ])
            ->findOrFail($category);

        $events = Event::with(['space', 'ticketTypes', 'tags'])
            ->where('type_events_id', $category->id)
            ->where('date', '>=', Carbon::now())
            ->where('active', 1)
            ->orderBy('date', 'asc')
            ->paginate(12);

        // Obtener todas las categorías para el sidebar/navegación
        $allCategories = TypeEvent::withCount([
            'events' => function ($query) {
                $query->where('date', '>=', Carbon::now())
                    ->where('active', 1);
            }
        ])
            ->having('events_count', '>', 0)
            ->orderBy('name')
            ->get();

        return view('public.categories.show', compact('category', 'events', 'allCategories'));
    }
}
