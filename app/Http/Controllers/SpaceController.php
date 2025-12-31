<?php

namespace App\Http\Controllers;

use App\Models\Space;
use App\Models\Event;
use Illuminate\Http\Request;
use App\Traits\S3ImageManager;

class SpaceController extends Controller
{
    use S3ImageManager;

    public function show(Request $request, $subdomain)
    {
        // Buscar el espacio por subdomain
        $space = Space::where('subdomain', $subdomain)->first();

        if (!$space) {
            abort(404, 'Espacio no encontrado');
        }

        // Verificar si el usuario es admin del espacio
        $isAdmin = auth()->check() && auth()->user()->isAdminOfSpace($space->id);

        // Obtener filtros de la request
        $tagFilter = $request->get('tag', null);
        $categoryFilter = $request->get('category', null);

        // Cargar relaciones necesarias
        $space->load(['events.ticketTypes', 'events.tags', 'events.type_event', 'users']);

        // Filtrar eventos si hay filtros
        $eventsQuery = Event::where('spaces_id', $space->id)
            ->where('active', true)
            ->where('date', '>=', now())
            ->with(['ticketTypes', 'tags', 'type_event']);

        if ($tagFilter) {
            $eventsQuery->whereHas('tags', function ($query) use ($tagFilter) {
                $query->where('tags.id', $tagFilter);
            });
        }

        if ($categoryFilter) {
            $eventsQuery->where('type_events_id', $categoryFilter);
        }

        $filteredEvents = $eventsQuery->get();

        // Obtener tags únicos de los eventos del espacio
        $allTags = \App\Models\Tag::whereHas('events', function ($query) use ($space) {
            $query->where('spaces_id', $space->id)->where('active', true);
        })->withCount([
                    'events' => function ($query) use ($space) {
                        $query->where('spaces_id', $space->id)->where('active', true);
                    }
                ])->get();

        // Obtener categorías únicas de los eventos del espacio
        $allCategories = \App\Models\TypeEvent::whereHas('events', function ($query) use ($space) {
            $query->where('spaces_id', $space->id)->where('active', true);
        })->withCount([
                    'events' => function ($query) use ($space) {
                        $query->where('spaces_id', $space->id)->where('active', true);
                    }
                ])->get();

        // Estadísticas generales del espacio
        $totalEvents = $space->events->count();
        $totalMembers = $space->users()->wherePivotNull('deleted_at')->count();

        // Obtener todos los eventos del espacio con sus IDs
        $eventIds = $space->events->pluck('id');

        // Estadísticas de boletos
        $totalTicketsAvailable = 0;
        $totalTicketsSold = 0;
        $totalRevenue = 0;

        foreach ($space->events as $event) {
            foreach ($event->ticketTypes as $ticketType) {
                $ticketEvent = \App\Models\TicketsEvent::where('event_id', $event->id)
                    ->where('ticket_types_id', $ticketType->id)
                    ->first();

                if ($ticketEvent) {
                    $totalTicketsAvailable += $ticketEvent->quantity;

                    // Contar boletos vendidos para este evento y tipo
                    $sold = \App\Models\Ticket::where('event_id', $event->id)
                        ->where('ticket_types_id', $ticketType->id)
                        ->count();

                    $totalTicketsSold += $sold;
                    $totalRevenue += $sold * $ticketEvent->price;
                }
            }
        }

        // Estadísticas de usuarios con sus boletos
        $usersWithStats = [];
        $spaceUsers = $space->users()->wherePivotNull('deleted_at')->get();

        foreach ($spaceUsers as $user) {
            // Obtener todas las órdenes del usuario
            $allUserOrders = \App\Models\Order::where('user_id', $user->id)
                ->with(['tickets', 'payments'])
                ->get();

            // Filtrar órdenes que pertenecen a eventos de este espacio
            $userOrders = $allUserOrders->filter(function ($order) use ($eventIds) {
                // Si event_id es un array JSON, verificar si contiene algún ID del espacio
                if (is_string($order->event_id)) {
                    $decoded = json_decode($order->event_id, true);
                    if (is_array($decoded)) {
                        return !empty(array_intersect($decoded, $eventIds->toArray()));
                    }
                    return in_array($order->event_id, $eventIds->toArray());
                }
                return in_array($order->event_id, $eventIds->toArray());
            });

            // Obtener tickets del usuario para eventos de este espacio
            $userTickets = \App\Models\Ticket::whereIn('event_id', $eventIds->toArray())
                ->whereHas('order', function ($query) use ($user) {
                    $query->where('user_id', $user->id);
                })
                ->get();

            $userTicketsOwned = $userTickets->count();
            $userRevenue = 0;

            // Calcular revenue de las órdenes filtradas
            foreach ($userOrders as $order) {
                $payment = $order->payments->first();
                if ($payment && $payment->total) {
                    $userRevenue += $payment->total;
                }
            }

            $userOrdersCount = $userOrders->count();

            // Obtener rol del usuario en el espacio
            $userSpace = $user->spaces()->where('spaces.id', $space->id)->first();
            $roleId = $userSpace ? $userSpace->pivot->role_space_id : null;
            $roleName = 'Miembro';
            if ($roleId) {
                $role = \App\Models\RoleSpace::find($roleId);
                $roleName = $role ? $role->name : 'Miembro';
            }

            $usersWithStats[] = [
                'user' => $user,
                'tickets_owned' => $userTicketsOwned,
                'revenue' => $userRevenue,
                'orders_count' => $userOrdersCount,
                'role' => $roleName,
                'is_admin' => $roleId == 1
            ];
        }

        // Ordenar usuarios por revenue descendente
        usort($usersWithStats, function ($a, $b) {
            return $b['revenue'] <=> $a['revenue'];
        });

        // ===== ENHANCED DASHBOARD DATA =====

        // Monthly Revenue Data (últimos 6 meses)
        $monthlyRevenueData = ['months' => [], 'revenues' => []];
        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $monthlyRevenueData['months'][] = $date->translatedFormat('M Y');

            // Obtener órdenes del mes para eventos de este espacio
            $monthRevenue = \App\Models\Payment::whereHas('order', function ($q) use ($eventIds, $date) {
                $q->whereIn('event_id', $eventIds->toArray())
                    ->whereMonth('created_at', $date->month)
                    ->whereYear('created_at', $date->year);
            })->sum('total');

            $monthlyRevenueData['revenues'][] = $monthRevenue ?: 0;
        }

        // Ticket Distribution by Type
        $ticketsByType = collect();
        $ticketTypeStats = [];
        foreach ($space->events as $event) {
            foreach ($event->ticketTypes as $ticketType) {
                $ticketEvent = \App\Models\TicketsEvent::where('event_id', $event->id)
                    ->where('ticket_types_id', $ticketType->id)
                    ->first();

                if ($ticketEvent) {
                    $sold = \App\Models\Ticket::where('event_id', $event->id)
                        ->where('ticket_types_id', $ticketType->id)
                        ->count();

                    $typeName = $ticketType->name;
                    if (!isset($ticketTypeStats[$typeName])) {
                        $ticketTypeStats[$typeName] = 0;
                    }
                    $ticketTypeStats[$typeName] += $sold;
                }
            }
        }
        foreach ($ticketTypeStats as $name => $sold) {
            $ticketsByType->push(['name' => $name, 'total_sold' => $sold]);
        }

        // Recent Orders (últimas 10 órdenes de eventos de este espacio)
        $recentOrders = \App\Models\Order::with(['user', 'payments', 'tickets'])
            ->whereHas('tickets', function ($q) use ($eventIds) {
                $q->whereIn('event_id', $eventIds->toArray());
            })
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get()
            ->map(function ($order) use ($eventIds) {
                // Obtener eventos de esta orden que pertenecen al espacio
                $orderEvents = \App\Models\Event::whereIn('id', $eventIds->toArray())
                    ->whereIn('id', $order->tickets->pluck('event_id')->unique())
                    ->get();
                $order->events = $orderEvents;
                return $order;
            });

        // Recent Check-ins (últimos 10 check-ins de eventos de este espacio)
        $recentCheckins = \App\Models\Checkin::with(['ticket.order.user'])
            ->whereHas('ticket', function ($q) use ($eventIds) {
                $q->whereIn('event_id', $eventIds->toArray());
            })
            ->orderBy('scanned_at', 'desc')
            ->take(10)
            ->get();

        // Total Check-ins
        $totalCheckins = \App\Models\Checkin::whereHas('ticket', function ($q) use ($eventIds) {
            $q->whereIn('event_id', $eventIds->toArray());
        })->count();

        // Check-in Rate
        $checkinRate = $totalTicketsSold > 0 ? ($totalCheckins / $totalTicketsSold) * 100 : 0;

        // Upcoming Events
        $upcomingEvents = Event::where('spaces_id', $space->id)
            ->where('active', true)
            ->where('date', '>=', now())
            ->orderBy('date', 'asc')
            ->take(5)
            ->get();

        // Total Orders
        $totalOrders = \App\Models\Order::whereHas('tickets', function ($q) use ($eventIds) {
            $q->whereIn('event_id', $eventIds->toArray());
        })->count();

        // Average Ticket Price
        $averageTicketPrice = $totalTicketsSold > 0 ? $totalRevenue / $totalTicketsSold : 0;

        // Daily Sales (últimos 7 días)
        $dailySalesData = ['days' => [], 'revenues' => []];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $dailySalesData['days'][] = $date->translatedFormat('D d');

            $dayRevenue = \App\Models\Payment::whereHas('order', function ($q) use ($eventIds, $date) {
                $q->whereHas('tickets', function ($q2) use ($eventIds) {
                    $q2->whereIn('event_id', $eventIds->toArray());
                })
                    ->whereDate('created_at', $date->toDateString());
            })->sum('total');

            $dailySalesData['revenues'][] = $dayRevenue ?: 0;
        }

        // Available Roles for user management
        $roleSpaces = \App\Models\RoleSpace::whereNull('deleted_at')->get();

        // All Permissions for role management
        $allPermissions = \App\Models\Permission::whereNull('deleted_at')->get();

        // ===== ORDERS TAB DATA =====
        $ordersSearch = $request->get('orders_search', '');
        $ordersQuery = \App\Models\Order::with(['user', 'payments', 'tickets.ticketType', 'tickets.event'])
            ->whereHas('tickets', function ($q) use ($eventIds) {
                $q->whereIn('event_id', $eventIds->toArray());
            });

        // Apply search filter
        if (!empty($ordersSearch)) {
            $ordersQuery->where(function ($q) use ($ordersSearch) {
                $q->where('id', 'like', "%{$ordersSearch}%")
                    ->orWhereHas('user', function ($uq) use ($ordersSearch) {
                        $uq->where('name', 'like', "%{$ordersSearch}%")
                            ->orWhere('email', 'like', "%{$ordersSearch}%")
                            ->orWhere('last_name', 'like', "%{$ordersSearch}%");
                    });
            });
        }

        $spaceOrders = $ordersQuery
            ->orderBy('created_at', 'desc')
            ->paginate(15)
            ->appends(['tab' => 'orders', 'orders_search' => $ordersSearch]);

        // ===== FOLLOW SYSTEM DATA =====
        // Follower count (users with role_space_id = 3 = viewer = follower)
        $followerCount = \App\Models\SpacesUser::where('space_id', $space->id)
            ->where('role_space_id', 3)
            ->whereNull('deleted_at')
            ->count();

        // Check if current user is following
        $isFollowing = false;
        if (auth()->check()) {
            $isFollowing = \App\Models\SpacesUser::where('space_id', $space->id)
                ->where('user_id', auth()->id())
                ->where('role_space_id', 3)
                ->whereNull('deleted_at')
                ->exists();
        }

        // Check if user is member (any role)
        $isMember = false;
        if (auth()->check()) {
            $isMember = \App\Models\SpacesUser::where('space_id', $space->id)
                ->where('user_id', auth()->id())
                ->whereNull('deleted_at')
                ->exists();
        }

        return view('spaces.profile', compact(
            'space',
            'isAdmin',
            'totalEvents',
            'totalMembers',
            'totalTicketsAvailable',
            'totalTicketsSold',
            'totalRevenue',
            'usersWithStats',
            'filteredEvents',
            'allTags',
            'allCategories',
            'tagFilter',
            'categoryFilter',
            // Enhanced Dashboard Data
            'monthlyRevenueData',
            'ticketsByType',
            'recentOrders',
            'recentCheckins',
            'totalCheckins',
            'checkinRate',
            'upcomingEvents',
            'totalOrders',
            'averageTicketPrice',
            'dailySalesData',
            'roleSpaces',
            'allPermissions',
            // Orders Tab Data
            'spaceOrders',
            // Follow System Data
            'followerCount',
            'isFollowing',
            'isMember'
        ));
    }

    public function edit(Request $request, $subdomain)
    {
        $space = Space::where('subdomain', $subdomain)->first();

        if (!$space) {
            abort(404, 'Espacio no encontrado');
        }

        // Verificar que el usuario autenticado es admin del espacio
        if (!auth()->user() || !auth()->user()->isAdminOfSpace($space->id)) {
            abort(403, 'No tienes permisos para editar este espacio. Solo los administradores pueden editar.');
        }

        return view('spaces.edit-profile', compact('space'));
    }

    public function update(Request $request, $subdomain)
    {
        $space = Space::where('subdomain', $subdomain)->first();

        if (!$space) {
            abort(404, 'Espacio no encontrado');
        }

        // Verificar que el usuario autenticado es admin del espacio
        if (!auth()->user() || !auth()->user()->isAdminOfSpace($space->id)) {
            abort(403, 'No tienes permisos para editar este espacio. Solo los administradores pueden editar.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'about' => 'nullable|string',
            'keywords' => 'nullable|string|max:1000',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'banner' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
            'color_primary' => 'nullable|string|max:7',
            'color_secondary' => 'nullable|string|max:7',
            'location' => 'nullable|string|max:255',
            'website' => 'nullable|url|max:255',
            'contact_email' => 'nullable|email|max:255',
            'contact_phone' => 'nullable|string|max:20',
        ]);

        // Actualizar campos básicos
        $space->update([
            'name' => $request->name,
            'description' => $request->description,
            'about' => $request->about,
            'keywords' => $request->keywords,
            'color_primary' => $request->color_primary,
            'color_secondary' => $request->color_secondary,
            'location' => $request->location,
            'website' => $request->website,
            'contact_email' => $request->contact_email,
            'contact_phone' => $request->contact_phone,
        ]);

        // Manejar upload de logo
        if ($request->hasFile('logo')) {

            // Si ya había un logo, eliminarlo del bucket
            if (!empty($space->logo)) {
                $oldLogo = basename($space->logo); // Obtiene solo el archivo.ext
                $this->deleteS3Image('spaces/logos', $oldLogo);
            }

            $fileContents = file_get_contents($request->file('logo')->getPathname());

            $logoUrl = $this->saveImages(
                $fileContents,
                'spaces/logos',
                $space->id
            );

            $space->update(['logo' => $logoUrl]);
        }

        if ($request->hasFile('banner')) {

            if (!empty($space->banner)) {
                $oldBanner = basename($space->banner);
                $this->deleteS3Image('spaces/banners', $oldBanner);
            }

            $fileContents = file_get_contents($request->file('banner')->getPathname());

            $bannerUrl = $this->saveImages(
                $fileContents,
                'spaces/banners',
                $space->id
            );

            $space->update(['banner' => $bannerUrl]);
        }

        return redirect()->route('spaces.profile', $space->subdomain)
            ->with('success', 'Perfil del cajón actualizado exitosamente');
    }

    public function updateProfile(Request $request, $subdomain)
    {
        $space = Space::where('subdomain', $subdomain)->first();

        if (!$space) {
            return response()->json(['success' => false, 'message' => 'Espacio no encontrado'], 404);
        }

        // Verificar que el usuario autenticado es admin del espacio
        if (!auth()->user() || !auth()->user()->isAdminOfSpace($space->id)) {
            return response()->json(['success' => false, 'message' => 'No tienes permisos para editar este espacio'], 403);
        }

        // Validación condicional - solo validar campos que están presentes
        $validationRules = [];

        if ($request->has('name')) {
            $validationRules['name'] = 'required|string|max:255';
        }
        if ($request->has('about')) {
            $validationRules['about'] = 'nullable|string';
        }
        if ($request->has('location')) {
            $validationRules['location'] = 'nullable|string|max:255';
        }
        if ($request->has('website')) {
            $validationRules['website'] = 'nullable|url|max:255';
        }
        if ($request->has('contact_email')) {
            $validationRules['contact_email'] = 'nullable|email|max:255';
        }
        if ($request->has('contact_phone')) {
            $validationRules['contact_phone'] = 'nullable|string|max:20';
        }
        if ($request->has('social_facebook')) {
            $validationRules['social_facebook'] = 'nullable|url|max:255';
        }
        if ($request->has('social_instagram')) {
            $validationRules['social_instagram'] = 'nullable|url|max:255';
        }
        if ($request->has('social_twitter')) {
            $validationRules['social_twitter'] = 'nullable|url|max:255';
        }
        if ($request->has('color_primary')) {
            $validationRules['color_primary'] = 'nullable|string|max:7';
        }
        if ($request->has('color_secondary')) {
            $validationRules['color_secondary'] = 'nullable|string|max:7';
        }
        if ($request->has('keywords')) {
            $validationRules['keywords'] = 'nullable|string|max:1000';
        }

        $request->validate($validationRules);

        // Actualizar solo los campos que están presentes
        $updateData = [];

        if ($request->has('name')) {
            $updateData['name'] = $request->name;
        }
        if ($request->has('about')) {
            $updateData['about'] = $request->about;
        }
        if ($request->has('location')) {
            $updateData['location'] = $request->location;
        }
        if ($request->has('website')) {
            $updateData['website'] = $request->website;
        }
        if ($request->has('contact_email')) {
            $updateData['contact_email'] = $request->contact_email;
        }
        if ($request->has('contact_phone')) {
            $updateData['contact_phone'] = $request->contact_phone;
        }
        if ($request->has('social_facebook')) {
            $updateData['social_facebook'] = $request->social_facebook;
        }
        if ($request->has('social_instagram')) {
            $updateData['social_instagram'] = $request->social_instagram;
        }
        if ($request->has('social_twitter')) {
            $updateData['social_twitter'] = $request->social_twitter;
        }
        if ($request->has('color_primary')) {
            $updateData['color_primary'] = $request->color_primary;
        }
        if ($request->has('color_secondary')) {
            $updateData['color_secondary'] = $request->color_secondary;
        }
        if ($request->has('keywords')) {
            $updateData['keywords'] = $request->keywords;
        }

        $space->update($updateData);

        return response()->json([
            'success' => true,
            'message' => 'Perfil actualizado exitosamente',
            'space' => $space
        ]);
    }
}
