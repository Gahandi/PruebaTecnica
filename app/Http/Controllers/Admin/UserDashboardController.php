<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Ticket;
use App\Models\Order;
use App\Models\Event;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserDashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:admin');
    }

    /**
     * Dashboard principal de usuarios
     */
    public function index()
    {
        $users = User::with(['orders.payments', 'orders.tickets'])
            ->withCount([
                'orders as total_orders',
                'orders as completed_orders' => function($query) {
                    $query->where('status', 'completed');
                }
            ])
            ->paginate(20);

        // Estadísticas generales
        $totalUsers = User::count();
        $totalTicketsSold = Ticket::count();
        $totalRevenue = Payment::where('status', 'completed')->sum('total');
        $totalEvents = Event::count();
        $activeUsers = User::whereHas('orders', function($query) {
            $query->where('status', 'completed');
        })->count();

        // Estadísticas por usuario
        foreach ($users as $user) {
            $user->load(['orders.tickets', 'orders.payments']);
            
            $user->total_tickets = $user->orders->sum(function($order) {
                return $order->tickets->count();
            });
            
            $user->total_spent = $user->orders->sum(function($order) {
                $payment = $order->payments->first();
                return $payment ? $payment->total : 0;
            });
            
            $user->tickets_used = $user->orders->sum(function($order) {
                return $order->tickets->where('used', true)->count();
            });
            
            $user->tickets_available = $user->orders->sum(function($order) {
                return $order->tickets->where('used', false)->count();
            });
        }

        return view('admin.users.dashboard', compact('users', 'totalUsers', 'totalTicketsSold', 'totalRevenue', 'totalEvents', 'activeUsers'));
    }

    /**
     * Ver detalles de un usuario específico
     */
    public function show(User $user)
    {
        $user->load([
            'orders.payments',
            'orders.tickets.ticketType',
            'orders.tickets.eventTicket',
            'orders.tickets.checkin'
        ]);

        // Estadísticas del usuario
        $stats = [
            'total_orders' => $user->orders->count(),
            'completed_orders' => $user->orders->where('status', 'completed')->count(),
            'pending_orders' => $user->orders->where('status', 'pending')->count(),
            'total_tickets' => $user->orders->sum(function($order) {
                return $order->tickets->count();
            }),
            'tickets_used' => $user->orders->sum(function($order) {
                return $order->tickets->where('used', true)->count();
            }),
            'tickets_available' => $user->orders->sum(function($order) {
                return $order->tickets->where('used', false)->count();
            }),
            'total_spent' => $user->orders->sum(function($order) {
                $payment = $order->payments->first();
                return $payment ? $payment->total : 0;
            }),
            'average_order_value' => $user->orders->count() > 0 
                ? $user->orders->sum(function($order) {
                    $payment = $order->payments->first();
                    return $payment ? $payment->total : 0;
                }) / $user->orders->count() 
                : 0,
        ];

        // Eventos únicos a los que ha asistido
        $eventsAttended = $user->orders->flatMap(function($order) {
            return $order->tickets->pluck('event_id');
        })->unique()->count();

        // Gráfico de compras por mes (últimos 6 meses)
        $purchasesByMonth = $user->orders()
            ->where('status', 'completed')
            ->where('created_at', '>=', now()->subMonths(6))
            ->select(
                DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'),
                DB::raw('COUNT(*) as count'),
                DB::raw('SUM((SELECT total FROM payments WHERE payments.order_id = orders.id LIMIT 1)) as total')
            )
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        return view('admin.users.show', compact('user', 'stats', 'eventsAttended', 'purchasesByMonth'));
    }
}

