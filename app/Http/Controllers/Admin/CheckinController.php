<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Checkin;
use App\Models\Event;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;

class CheckinController extends Controller
{
    /**
     * Display a listing of check-ins
     */
    public function index(Request $request)
    {
        $query = Checkin::with(['ticket.order.user', 'ticket.order.event', 'user']);

        // Filter by event
        if ($request->filled('event_id')) {
            $query->whereHas('ticket.order', function ($q) use ($request) {
                $q->where('event_id', $request->event_id);
            });
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Search by user or ticket
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('ticket_id', 'like', "%{$search}%")
                    ->orWhereHas('ticket.order.user', function ($q2) use ($search) {
                        $q2->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    });
            });
        }

        // Order
        $query->latest();

        $checkins = $query->paginate(50);

        // Get events for filter
        $events = Event::select('id', 'name')->orderBy('name')->get();

        // Stats
        $stats = [
            'total' => Checkin::count(),
            'today' => Checkin::whereDate('created_at', today())->count(),
            'this_week' => Checkin::where('created_at', '>=', now()->startOfWeek())->count(),
            'this_month' => Checkin::whereMonth('created_at', now()->month)->count(),
        ];

        return view('admin.checkins.index', compact('checkins', 'events', 'stats'));
    }

    /**
     * Display check-in statistics
     */
    public function stats(Request $request)
    {
        // Check-ins by event
        $checkinsByEvent = DB::table('checkins')
            ->join('tickets', 'checkins.ticket_id', '=', 'tickets.id')
            ->join('orders', 'tickets.order_id', '=', 'orders.id')
            ->join('events', 'orders.event_id', '=', 'events.id')
            ->select('events.name', DB::raw('COUNT(checkins.id) as count'))
            ->groupBy('events.id', 'events.name')
            ->orderBy('count', 'desc')
            ->limit(10)
            ->get();

        // Check-ins by hour (last 24 hours)
        $checkinsByHour = Checkin::select(
            DB::raw('HOUR(created_at) as hour'),
            DB::raw('COUNT(*) as count')
        )
            ->where('created_at', '>=', now()->subDay())
            ->groupBy('hour')
            ->orderBy('hour')
            ->get();

        // Check-ins by day (last 30 days)
        $checkinsByDay = Checkin::select(
            DB::raw('DATE(created_at) as date'),
            DB::raw('COUNT(*) as count')
        )
            ->where('created_at', '>=', now()->subDays(30))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Top scanners
        $topScanners = DB::table('checkins')
            ->join('users', 'checkins.scanned_by', '=', 'users.id')
            ->select('users.name', DB::raw('COUNT(checkins.id) as count'))
            ->groupBy('users.id', 'users.name')
            ->orderBy('count', 'desc')
            ->limit(10)
            ->get();

        // Conversion rate (tickets sold vs checked in)
        $totalTickets = DB::table('tickets')
            ->join('orders', 'tickets.order_id', '=', 'orders.id')
            ->where('orders.status', 'completed')
            ->count();

        $checkedInTickets = Checkin::distinct('ticket_id')->count('ticket_id');
        $conversionRate = $totalTickets > 0 ? ($checkedInTickets / $totalTickets) * 100 : 0;

        return view('admin.checkins.stats', compact(
            'checkinsByEvent',
            'checkinsByHour',
            'checkinsByDay',
            'topScanners',
            'totalTickets',
            'checkedInTickets',
            'conversionRate'
        ));
    }

    /**
     * Export check-ins to CSV
     */
    public function export(Request $request)
    {
        $query = Checkin::with(['ticket.order.user', 'ticket.order.event', 'user']);

        // Apply filters
        if ($request->filled('event_id')) {
            $query->whereHas('ticket.order', function ($q) use ($request) {
                $q->where('event_id', $request->event_id);
            });
        }
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $checkins = $query->latest()->get();

        $filename = 'checkins_' . now()->format('Y-m-d_His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($checkins) {
            $file = fopen('php://output', 'w');

            // Headers
            fputcsv($file, [
                'ID',
                'Ticket ID',
                'Evento',
                'Usuario',
                'Email',
                'Escaneado Por',
                'Fecha Check-in'
            ]);

            // Data
            foreach ($checkins as $checkin) {
                fputcsv($file, [
                    $checkin->id,
                    $checkin->ticket_id,
                    $checkin->ticket->order->event->name ?? 'N/A',
                    $checkin->ticket->order->user->name ?? 'N/A',
                    $checkin->ticket->order->user->email ?? 'N/A',
                    $checkin->user->name ?? 'Sistema',
                    $checkin->created_at->format('Y-m-d H:i:s'),
                ]);
            }

            fclose($file);
        };

        ActivityLog::log('exported', 'Exported check-ins data', null, null, [
            'count' => $checkins->count()
        ]);

        return Response::stream($callback, 200, $headers);
    }

    /**
     * Show detailed check-in information
     */
    public function show(Checkin $checkin)
    {
        $checkin->load(['ticket.order.user', 'ticket.order.event', 'ticket.ticketType', 'user']);

        return view('admin.checkins.show', compact('checkin'));
    }
}
