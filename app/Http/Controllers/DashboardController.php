<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Order;
use App\Models\Ticket;
use App\Models\Coupon;
use App\Models\Checkin;
use App\Models\User;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

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
        $ticketsByType = DB::table('order_items')
            ->join('ticket_types', 'order_items.ticket_type_id', '=', 'ticket_types.id')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->where('orders.status', 'completed')
            ->select(
                'ticket_types.name',
                DB::raw('SUM(order_items.quantity) as total_sold'),
                DB::raw('SUM(order_items.price * order_items.quantity) as total_revenue')
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
            ->with([
                'ticketTypes' => function ($query) {
                    $query->select('ticket_types.id', 'ticket_types.event_id')
                        ->join('order_items', 'ticket_types.id', '=', 'order_items.ticket_type_id')
                        ->join('orders', 'order_items.order_id', '=', 'orders.id')
                        ->where('orders.status', 'completed')
                        ->selectRaw('SUM(order_items.price * order_items.quantity) as revenue');
                }
            ])
            ->orderBy('orders_count', 'desc')
            ->limit(5)
            ->get()
            ->map(function ($event) {
                $revenue = DB::table('order_items')
                    ->join('orders', 'order_items.order_id', '=', 'orders.id')
                    ->join('ticket_types', 'order_items.ticket_type_id', '=', 'ticket_types.id')
                    ->where('ticket_types.event_id', $event->id)
                    ->where('orders.status', 'completed')
                    ->sum(DB::raw('order_items.price * order_items.quantity'));

                $event->revenue = $revenue;
                return $event;
            });

        // ========== TOP COMPRADORES ==========
        $topBuyers = User::select('users.*')
            ->join('orders', 'users.id', '=', 'orders.user_id')
            ->join('payments', 'orders.id', '=', 'payments.order_id')
            ->where('orders.status', 'completed')
            ->groupBy('users.id', 'users.name', 'users.email', 'users.created_at', 'users.updated_at', 'users.email_verified_at', 'users.password', 'users.remember_token', 'users.last_name', 'users.phone')
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
                DB::raw('SUM(payments.total * coupons.discount_percentage / 100) as total_discount')
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
            'upcomingEvents'
        ));
    }
}
