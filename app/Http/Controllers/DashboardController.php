<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Order;
use App\Models\Ticket;
use App\Models\Coupon;
use App\Models\Checkin;
use App\Models\User;
use App\Models\Payment;
use App\Models\TicketType;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class DashboardController extends Controller
{
    public function index()
    {
        // ========== MÉTRICAS PRINCIPALES ==========
        $totalEvents = Event::count();
        $activeEvents = Event::where('active', true)->where('date', '>=', now())->count();
        $totalOrders = Order::count();
        $completedOrders = Order::where('status', 'completed')->count();
        $totalTickets = Ticket::count();
        $totalRevenue = Payment::whereHas('order', function ($query) {
            $query->where('status', 'completed');
        })->sum('total');
        $totalCheckins = Checkin::count();
        $totalUsers = User::count();

        // ========== MÉTRICAS DEL PERÍODO ANTERIOR (30 días) ==========
        $previousPeriodStart = now()->subDays(60);
        $previousPeriodEnd = now()->subDays(30);
        $currentPeriodStart = now()->subDays(30);

        $previousRevenue = Payment::whereHas('order', function ($query) {
            $query->where('status', 'completed');
        })
            ->whereBetween('created_at', [$previousPeriodStart, $previousPeriodEnd])
            ->sum('total');

        $currentRevenue = Payment::whereHas('order', function ($query) {
            $query->where('status', 'completed');
        })
            ->where('created_at', '>=', $currentPeriodStart)
            ->sum('total');

        $revenueGrowth = $previousRevenue > 0
            ? (($currentRevenue - $previousRevenue) / $previousRevenue) * 100
            : 0;

        $previousOrders = Order::where('status', 'completed')
            ->whereBetween('created_at', [$previousPeriodStart, $previousPeriodEnd])
            ->count();

        $currentOrders = Order::where('status', 'completed')
            ->where('created_at', '>=', $currentPeriodStart)
            ->count();

        $ordersGrowth = $previousOrders > 0
            ? (($currentOrders - $previousOrders) / $previousOrders) * 100
            : 0;

        // ========== TASA DE CONVERSIÓN ==========
        $conversionRate = $totalOrders > 0 ? ($completedOrders / $totalOrders) * 100 : 0;
        $averageOrderValue = $completedOrders > 0 ? $totalRevenue / $completedOrders : 0;

        // ========== INGRESOS POR MES (últimos 6 meses) ==========
        $monthlyRevenue = Payment::select(
            DB::raw('DATE_FORMAT(payments.created_at, "%Y-%m") as month'),
            DB::raw('SUM(payments.total) as revenue'),
            DB::raw('COUNT(DISTINCT payments.order_id) as orders_count')
        )
            ->join('orders', 'payments.order_id', '=', 'orders.id')
            ->where('payments.created_at', '>=', now()->subMonths(6))
            ->where('orders.status', 'completed')
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        $months = [];
        $revenues = [];
        $ordersPerMonth = [];

        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i)->format('Y-m');
            $months[] = now()->subMonths($i)->format('M Y');

            $revenue = $monthlyRevenue->where('month', $month)->first();
            $revenues[] = $revenue ? (float) $revenue->revenue : 0;
            $ordersPerMonth[] = $revenue ? (int) $revenue->orders_count : 0;
        }

        $monthlyRevenueData = [
            'months' => $months,
            'revenues' => $revenues,
            'orders' => $ordersPerMonth
        ];

        // ========== VENTAS POR DÍA (últimos 7 días) ==========
        $dailySales = Payment::select(
            DB::raw('DATE(payments.created_at) as date'),
            DB::raw('SUM(payments.total) as revenue'),
            DB::raw('COUNT(DISTINCT payments.order_id) as orders_count')
        )
            ->join('orders', 'payments.order_id', '=', 'orders.id')
            ->where('payments.created_at', '>=', now()->subDays(7))
            ->where('orders.status', 'completed')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $days = [];
        $dailyRevenues = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $days[] = now()->subDays($i)->format('D');

            $sale = $dailySales->where('date', $date)->first();
            $dailyRevenues[] = $sale ? (float) $sale->revenue : 0;
        }

        $dailySalesData = [
            'days' => $days,
            'revenues' => $dailyRevenues
        ];

        // ========== BOLETOS POR TIPO ==========
        // Usar la tabla tickets con join a tickets_events para obtener el precio
        $ticketsByType = DB::table('tickets')
            ->join('orders', 'tickets.order_id', '=', 'orders.id')
            ->join('ticket_types', 'tickets.ticket_types_id', '=', 'ticket_types.id')
            ->join('tickets_events', function ($join) {
                $join->on('tickets.ticket_types_id', '=', 'tickets_events.ticket_types_id')
                    ->on('tickets.event_id', '=', 'tickets_events.event_id');
            })
            ->where('orders.status', 'completed')
            ->select(
                'ticket_types.name',
                DB::raw('COUNT(tickets.id) as total_sold'),
                DB::raw('SUM(tickets_events.price) as total_revenue')
            )
            ->groupBy('ticket_types.id', 'ticket_types.name')
            ->orderBy('total_sold', 'desc')
            ->get();

        // ========== EVENTOS MÁS POPULARES ==========
        $popularEvents = Event::withCount([
            'orders' => function ($query) {
                $query->where('status', 'completed');
            }
        ])
            ->orderBy('orders_count', 'desc')
            ->limit(5)
            ->get()
            ->map(function ($event) {
                // Calcular revenue desde payments
                $revenue = Payment::whereHas('order', function ($query) use ($event) {
                    $query->where('event_id', $event->id)
                        ->where('status', 'completed');
                })->sum('total');

                $event->revenue = $revenue;
                return $event;
            });

        // ========== TOP COMPRADORES ==========
        $topBuyers = User::select('users.*')
            ->join('orders', 'users.id', '=', 'orders.user_id')
            ->join('payments', 'orders.id', '=', 'payments.order_id')
            ->where('orders.status', 'completed')
            ->groupBy('users.id', 'users.name', 'users.email', 'users.created_at', 'users.updated_at', 'users.email_verified_at', 'users.password', 'users.remember_token', 'users.last_name', 'users.phone', 'users.deleted_at', 'users.image', 'users.verified', 'users.verified_at', 'users.verification_code', 'users.role')
            ->selectRaw('users.*, COUNT(DISTINCT orders.id) as orders_count, SUM(payments.total) as total_spent')
            ->orderBy('total_spent', 'desc')
            ->limit(5)
            ->get();

        // ========== CUPONES MÁS USADOS ==========
        $couponsUsed = DB::table('payments')
            ->join('coupons', 'payments.coupon_id', '=', 'coupons.id')
            ->join('orders', 'payments.order_id', '=', 'orders.id')
            ->where('orders.status', 'completed')
            ->select(
                'coupons.code',
                'coupons.discount_percentage',
                DB::raw('COUNT(*) as times_used'),
                DB::raw('SUM(payments.discount_amount) as total_discount')
            )
            ->groupBy('coupons.id', 'coupons.code', 'coupons.discount_percentage')
            ->orderBy('times_used', 'desc')
            ->limit(5)
            ->get();

        // ========== CHECK-INS RECIENTES ==========
        $recentCheckins = Checkin::with(['ticket.order.event', 'ticket.order.user'])
            ->latest()
            ->limit(10)
            ->get();

        // ========== ACTIVIDAD RECIENTE ==========
        $recentOrders = Order::with(['user', 'event'])
            ->where('status', 'completed')
            ->latest()
            ->limit(5)
            ->get();

        // ========== PRÓXIMOS EVENTOS ==========
        $upcomingEvents = Event::where('active', true)
            ->where('date', '>=', now())
            ->orderBy('date', 'asc')
            ->limit(5)
            ->get();

        // ========== ESTADÍSTICAS DE CHECK-IN ==========
        $checkinRate = $totalTickets > 0 ? ($totalCheckins / $totalTickets) * 100 : 0;

        // ========== ESTADÍSTICAS DE USUARIOS ==========
        $userStats = [
            'total' => User::count(),
            'admins' => User::where('role', 'admin')->count(),
            'staff' => User::where('role', 'staff')->count(),
            'regular' => User::where('role', 'user')->count(),
            'verified' => User::whereNotNull('verified_at')->count(),
            'new_today' => User::whereDate('created_at', today())->count(),
            'new_this_week' => User::where('created_at', '>=', now()->startOfWeek())->count(),
            'new_this_month' => User::whereMonth('created_at', now()->month)->count(),
        ];

        // User growth chart (last 7 days)
        $userGrowthData = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $userGrowthData['labels'][] = $date->format('D');
            $userGrowthData['data'][] = User::whereDate('created_at', $date->format('Y-m-d'))->count();
        }

        // ========== ACTIVITY LOG ==========
        $recentActivity = ActivityLog::with('user')
            ->latest()
            ->limit(15)
            ->get();

        // Activity by action (last 7 days)
        $activityByAction = ActivityLog::select('action', DB::raw('count(*) as count'))
            ->where('created_at', '>=', now()->subDays(7))
            ->groupBy('action')
            ->orderBy('count', 'desc')
            ->get();

        // ========== ALERTAS Y NOTIFICACIONES ==========
        $alerts = [];

        // Eventos próximos a iniciar (próximas 24 horas)
        $upcomingEventsAlert = Event::where('active', true)
            ->whereBetween('date', [now(), now()->addDay()])
            ->count();
        if ($upcomingEventsAlert > 0) {
            $alerts[] = [
                'type' => 'info',
                'message' => "{$upcomingEventsAlert} evento(s) comenzarán en las próximas 24 horas",
                'icon' => '📅'
            ];
        }

        // Usuarios sin verificar
        $unverifiedUsers = User::whereNull('verified_at')->count();
        if ($unverifiedUsers > 10) {
            $alerts[] = [
                'type' => 'warning',
                'message' => "{$unverifiedUsers} usuarios pendientes de verificación",
                'icon' => '⚠️'
            ];
        }

        // Órdenes pendientes
        $pendingOrders = Order::where('status', 'pending')->count();
        if ($pendingOrders > 0) {
            $alerts[] = [
                'type' => 'warning',
                'message' => "{$pendingOrders} órdenes pendientes de procesar",
                'icon' => '🔔'
            ];
        }

        return view('dashboard', compact(
            // Métricas principales
            'totalEvents',
            'activeEvents',
            'totalOrders',
            'completedOrders',
            'totalTickets',
            'totalRevenue',
            'totalCheckins',
            'totalUsers',
            // Crecimiento
            'revenueGrowth',
            'ordersGrowth',
            // Conversión y promedios
            'conversionRate',
            'averageOrderValue',
            'checkinRate',
            // Datos de gráficos
            'monthlyRevenueData',
            'dailySalesData',
            'ticketsByType',
            // Rankings y tops
            'popularEvents',
            'topBuyers',
            'couponsUsed',
            // Actividad
            'recentCheckins',
            'recentOrders',
            'upcomingEvents',
            // Nuevas métricas
            'userStats',
            'userGrowthData',
            'recentActivity',
            'activityByAction',
            'alerts'
        ));
    }

    /**
     * Export dashboard to PDF
     */
    public function exportPdf()
    {
        // Get all the same data as index
        $totalEvents = Event::count();
        $activeEvents = Event::where('active', true)->where('date', '>=', now())->count();
        $totalOrders = Order::count();
        $completedOrders = Order::where('status', 'completed')->count();
        $totalTickets = Ticket::count();
        $totalRevenue = Payment::whereHas('order', function ($query) {
            $query->where('status', 'completed');
        })->sum('total');
        $totalCheckins = Checkin::count();
        $totalUsers = User::count();

        // Growth metrics
        $previousPeriodStart = now()->subDays(60);
        $previousPeriodEnd = now()->subDays(30);
        $currentPeriodStart = now()->subDays(30);

        $previousRevenue = Payment::whereHas('order', function ($query) {
            $query->where('status', 'completed');
        })
            ->whereBetween('created_at', [$previousPeriodStart, $previousPeriodEnd])
            ->sum('total');

        $currentRevenue = Payment::whereHas('order', function ($query) {
            $query->where('status', 'completed');
        })
            ->where('created_at', '>=', $currentPeriodStart)
            ->sum('total');

        $revenueGrowth = $previousRevenue > 0
            ? (($currentRevenue - $previousRevenue) / $previousRevenue) * 100
            : 0;

        $conversionRate = $totalOrders > 0 ? ($completedOrders / $totalOrders) * 100 : 0;
        $averageOrderValue = $completedOrders > 0 ? $totalRevenue / $completedOrders : 0;
        $checkinRate = $totalTickets > 0 ? ($totalCheckins / $totalTickets) * 100 : 0;

        // Popular events
        $popularEvents = Event::withCount([
            'orders' => function ($query) {
                $query->where('status', 'completed');
            }
        ])
            ->orderBy('orders_count', 'desc')
            ->limit(10)
            ->get()
            ->map(function ($event) {
                $revenue = Payment::whereHas('order', function ($query) use ($event) {
                    $query->where('event_id', $event->id)
                        ->where('status', 'completed');
                })->sum('total');
                $event->revenue = $revenue;
                return $event;
            });

        // Top buyers
        $topBuyers = User::select('users.*')
            ->join('orders', 'users.id', '=', 'orders.user_id')
            ->join('payments', 'orders.id', '=', 'payments.order_id')
            ->where('orders.status', 'completed')
            ->groupBy('users.id', 'users.name', 'users.email', 'users.created_at', 'users.updated_at', 'users.email_verified_at', 'users.password', 'users.remember_token', 'users.last_name', 'users.phone', 'users.deleted_at', 'users.image', 'users.verified', 'users.verified_at', 'users.verification_code', 'users.role')
            ->selectRaw('users.*, COUNT(DISTINCT orders.id) as orders_count, SUM(payments.total) as total_spent')
            ->orderBy('total_spent', 'desc')
            ->limit(10)
            ->get();

        // User stats
        $userStats = [
            'total' => User::count(),
            'admins' => User::where('role', 'admin')->count(),
            'staff' => User::where('role', 'staff')->count(),
            'regular' => User::where('role', 'user')->count(),
            'verified' => User::whereNotNull('verified_at')->count(),
        ];

        $pdf = Pdf::loadView('admin.reports.pdf.dashboard', compact(
            'totalEvents',
            'activeEvents',
            'totalOrders',
            'completedOrders',
            'totalTickets',
            'totalRevenue',
            'totalCheckins',
            'totalUsers',
            'revenueGrowth',
            'conversionRate',
            'averageOrderValue',
            'checkinRate',
            'popularEvents',
            'topBuyers',
            'userStats'
        ));

        ActivityLog::log('exported', 'Exported dashboard to PDF');

        return $pdf->download('dashboard-' . now()->format('Y-m-d') . '.pdf');
    }
}
