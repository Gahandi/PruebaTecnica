<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Order;
use App\Models\Ticket;
use App\Models\Coupon;
use App\Models\Checkin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // Métricas generales
        $totalEvents = Event::count();
        $totalOrders = Order::count();
        $totalTickets = Ticket::count();
        // El total está en la tabla payments, no en orders
        $totalRevenue = \App\Models\Payment::whereHas('order', function($query) {
            $query->where('status', 'completed');
        })->sum('total');
        $totalCheckins = Checkin::count();
        
        // Ingresos por mes (últimos 6 meses) - desde payments
        $monthlyRevenue = \App\Models\Payment::select(
                DB::raw('DATE_FORMAT(payments.created_at, "%Y-%m") as month'),
                DB::raw('SUM(payments.total) as revenue')
            )
            ->join('orders', 'payments.order_id', '=', 'orders.id')
            ->where('payments.created_at', '>=', now()->subMonths(6))
            ->where('orders.status', 'completed')
            ->groupBy('month')
            ->orderBy('month')
            ->get();
        
        // Asegurar que tengamos datos para los últimos 6 meses
        $months = [];
        $revenues = [];
        
        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i)->format('Y-m');
            $months[] = now()->subMonths($i)->format('M Y');
            
            $revenue = $monthlyRevenue->where('month', $month)->first();
            $revenues[] = $revenue ? (float)$revenue->revenue : 0;
        }
        
        $monthlyRevenueData = [
            'months' => $months,
            'revenues' => $revenues
        ];
        
        // Boletos vendidos por tipo
        $ticketsByType = DB::table('order_items')
            ->join('ticket_types', 'order_items.ticket_type_id', '=', 'ticket_types.id')
            ->select('ticket_types.name', DB::raw('SUM(order_items.quantity) as total_sold'))
            ->groupBy('ticket_types.id', 'ticket_types.name')
            ->get();
        
        // Cupones más usados - desde payments
        $couponsUsed = DB::table('payments')
            ->join('coupons', 'payments.coupon_id', '=', 'coupons.id')
            ->join('orders', 'payments.order_id', '=', 'orders.id')
            ->where('orders.status', 'completed')
            ->select('coupons.code', 'coupons.discount_percentage', DB::raw('COUNT(*) as times_used'))
            ->groupBy('coupons.id', 'coupons.code', 'coupons.discount_percentage')
            ->orderBy('times_used', 'desc')
            ->get();
        
        // Eventos más populares - contar órdenes completadas
        $popularEvents = Event::withCount(['orders' => function($query) {
            $query->where('status', 'completed');
        }])
            ->orderBy('orders_count', 'desc')
            ->limit(5)
            ->get();
        
        // Check-ins recientes
        $recentCheckins = Checkin::with(['ticket.order.event'])
            ->latest()
            ->limit(10)
            ->get();
        
        return view('dashboard', compact(
            'totalEvents',
            'totalOrders', 
            'totalTickets',
            'totalRevenue',
            'totalCheckins',
            'monthlyRevenueData',
            'ticketsByType',
            'couponsUsed',
            'popularEvents',
            'recentCheckins'
        ));
    }
}
