<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Space;

class AdminSpaceController extends Controller
{
    public function index()
    {
        $spaces = Space::with([
            'users' => function ($q) {
                $q->wherePivotNull('deleted_at');
            },
            'events' => function ($q) {
                $q->with('type_event')
                    ->withCount(['orders', 'tickets_events'])
                    ->latest()
                    ->take(6);
            }
        ])
            ->withCount(['events', 'users'])
            ->latest()
            ->paginate(10);

        // Add orders count for each space
        foreach ($spaces as $space) {
            $space->orders_count = $space->events->sum('orders_count');
        }

        return view('admin.spaces.index', compact('spaces'));
    }
}
