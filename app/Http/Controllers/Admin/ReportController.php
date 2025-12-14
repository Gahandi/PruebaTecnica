<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Order;
use App\Models\Event;
use App\Models\Checkin;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\SalesReportExport;
use App\Exports\UsersReportExport;
use App\Exports\CheckinsReportExport;

class ReportController extends Controller
{
    /**
     * Display reports dashboard
     */
    public function index()
    {
        $stats = [
            'total_sales' => Order::where('status', 'completed')->sum('total'),
            'total_orders' => Order::where('status', 'completed')->count(),
            'total_users' => User::count(),
            'total_checkins' => Checkin::count(),
        ];

        return view('admin.reports.index', compact('stats'));
    }

    /**
     * Sales report
     */
    public function sales(Request $request)
    {
        $period = $request->get('period', 'month'); // day, week, month, year
        $startDate = $request->get('start_date', now()->startOfMonth());
        $endDate = $request->get('end_date', now()->endOfMonth());

        // Parse dates
        $startDate = Carbon::parse($startDate);
        $endDate = Carbon::parse($endDate);

        // Get sales data
        $sales = Order::where('status', 'completed')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->with(['event', 'user'])
            ->get();

        // Calculate statistics
        $stats = [
            'total_sales' => $sales->sum('total'),
            'total_orders' => $sales->count(),
            'average_order' => $sales->avg('total'),
            'total_tickets' => DB::table('tickets')
                ->join('orders', 'tickets.order_id', '=', 'orders.id')
                ->where('orders.status', 'completed')
                ->whereBetween('orders.created_at', [$startDate, $endDate])
                ->count(),
        ];

        // Sales by event
        $salesByEvent = Order::select('event_id', DB::raw('SUM(total) as total_sales'), DB::raw('COUNT(*) as total_orders'))
            ->where('status', 'completed')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('event_id')
            ->with('event')
            ->orderBy('total_sales', 'desc')
            ->get();

        // Sales by day (for chart)
        $salesByDay = Order::select(
            DB::raw('DATE(created_at) as date'),
            DB::raw('SUM(total) as total'),
            DB::raw('COUNT(*) as count')
        )
            ->where('status', 'completed')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Previous period comparison
        $periodDiff = $startDate->diffInDays($endDate);
        $prevStartDate = $startDate->copy()->subDays($periodDiff);
        $prevEndDate = $startDate->copy()->subDay();

        $prevSales = Order::where('status', 'completed')
            ->whereBetween('created_at', [$prevStartDate, $prevEndDate])
            ->sum('total');

        $growth = $prevSales > 0 ? (($stats['total_sales'] - $prevSales) / $prevSales) * 100 : 0;

        ActivityLog::log('viewed', 'Viewed sales report', null, null, [
            'period' => $period,
            'start_date' => $startDate->format('Y-m-d'),
            'end_date' => $endDate->format('Y-m-d')
        ]);

        return view('admin.reports.sales', compact(
            'sales',
            'stats',
            'salesByEvent',
            'salesByDay',
            'growth',
            'period',
            'startDate',
            'endDate'
        ));
    }

    /**
     * Users report
     */
    public function users(Request $request)
    {
        $period = $request->get('period', 'month');
        $startDate = Carbon::parse($request->get('start_date', now()->startOfMonth()));
        $endDate = Carbon::parse($request->get('end_date', now()->endOfMonth()));

        // Get users data
        $users = User::whereBetween('created_at', [$startDate, $endDate])->get();

        // Statistics
        $stats = [
            'total_users' => $users->count(),
            'admins' => $users->where('role', 'admin')->count(),
            'staff' => $users->where('role', 'staff')->count(),
            'regular' => $users->where('role', 'user')->count(),
            'verified' => $users->whereNotNull('verified_at')->count(),
            'unverified' => $users->whereNull('verified_at')->count(),
        ];

        // Users by day
        $usersByDay = User::select(
            DB::raw('DATE(created_at) as date'),
            DB::raw('COUNT(*) as count')
        )
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Users by role
        $usersByRole = User::select('role', DB::raw('COUNT(*) as count'))
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('role')
            ->get();

        // Top users by orders
        $topUsers = User::select('users.*', DB::raw('COUNT(orders.id) as orders_count'), DB::raw('SUM(orders.total) as total_spent'))
            ->join('orders', 'users.id', '=', 'orders.user_id')
            ->where('orders.status', 'completed')
            ->whereBetween('orders.created_at', [$startDate, $endDate])
            ->groupBy('users.id')
            ->orderBy('total_spent', 'desc')
            ->limit(10)
            ->get();

        ActivityLog::log('viewed', 'Viewed users report', null, null, [
            'period' => $period,
            'start_date' => $startDate->format('Y-m-d'),
            'end_date' => $endDate->format('Y-m-d')
        ]);

        return view('admin.reports.users', compact(
            'users',
            'stats',
            'usersByDay',
            'usersByRole',
            'topUsers',
            'period',
            'startDate',
            'endDate'
        ));
    }

    /**
     * Check-ins report
     */
    public function checkins(Request $request)
    {
        $period = $request->get('period', 'month');
        $startDate = Carbon::parse($request->get('start_date', now()->startOfMonth()));
        $endDate = Carbon::parse($request->get('end_date', now()->endOfMonth()));
        $eventId = $request->get('event_id');

        // Get check-ins data
        $query = Checkin::whereBetween('created_at', [$startDate, $endDate])
            ->with(['ticket.order.event', 'user']);

        if ($eventId) {
            $query->whereHas('ticket.order', function ($q) use ($eventId) {
                $q->where('event_id', $eventId);
            });
        }

        $checkins = $query->get();

        // Statistics
        $stats = [
            'total_checkins' => $checkins->count(),
            'unique_users' => $checkins->pluck('user_id')->unique()->count(),
            'unique_events' => $checkins->pluck('ticket.order.event_id')->unique()->count(),
        ];

        // Check-ins by event
        $checkinsByEvent = DB::table('checkins')
            ->join('tickets', 'checkins.ticket_id', '=', 'tickets.id')
            ->join('orders', 'tickets.order_id', '=', 'orders.id')
            ->join('events', 'orders.event_id', '=', 'events.id')
            ->select('events.name', 'events.id', DB::raw('COUNT(checkins.id) as count'))
            ->whereBetween('checkins.created_at', [$startDate, $endDate])
            ->groupBy('events.id', 'events.name')
            ->orderBy('count', 'desc')
            ->get();

        // Check-ins by hour
        $checkinsByHour = Checkin::select(
            DB::raw('HOUR(created_at) as hour'),
            DB::raw('COUNT(*) as count')
        )
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('hour')
            ->orderBy('hour')
            ->get();

        // Check-ins by day
        $checkinsByDay = Checkin::select(
            DB::raw('DATE(created_at) as date'),
            DB::raw('COUNT(*) as count')
        )
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Get events for filter
        $events = Event::orderBy('name')->get();

        ActivityLog::log('viewed', 'Viewed check-ins report', null, null, [
            'period' => $period,
            'start_date' => $startDate->format('Y-m-d'),
            'end_date' => $endDate->format('Y-m-d'),
            'event_id' => $eventId
        ]);

        return view('admin.reports.checkins', compact(
            'checkins',
            'stats',
            'checkinsByEvent',
            'checkinsByHour',
            'checkinsByDay',
            'events',
            'period',
            'startDate',
            'endDate',
            'eventId'
        ));
    }

    /**
     * Export sales report to PDF
     */
    public function exportSalesPdf(Request $request)
    {
        $startDate = Carbon::parse($request->get('start_date', now()->startOfMonth()));
        $endDate = Carbon::parse($request->get('end_date', now()->endOfMonth()));

        $sales = Order::where('status', 'completed')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->with(['event', 'user'])
            ->get();

        $stats = [
            'total_sales' => $sales->sum('total'),
            'total_orders' => $sales->count(),
            'average_order' => $sales->avg('total'),
        ];

        $pdf = Pdf::loadView('admin.reports.pdf.sales', compact('sales', 'stats', 'startDate', 'endDate'));

        ActivityLog::log('exported', 'Exported sales report to PDF', null, null, [
            'start_date' => $startDate->format('Y-m-d'),
            'end_date' => $endDate->format('Y-m-d')
        ]);

        return $pdf->download('sales-report-' . now()->format('Y-m-d') . '.pdf');
    }

    /**
     * Export sales report to Excel
     */
    public function exportSalesExcel(Request $request)
    {
        $startDate = Carbon::parse($request->get('start_date', now()->startOfMonth()));
        $endDate = Carbon::parse($request->get('end_date', now()->endOfMonth()));

        ActivityLog::log('exported', 'Exported sales report to Excel', null, null, [
            'start_date' => $startDate->format('Y-m-d'),
            'end_date' => $endDate->format('Y-m-d')
        ]);

        return Excel::download(
            new SalesReportExport($startDate, $endDate),
            'sales-report-' . now()->format('Y-m-d') . '.xlsx'
        );
    }

    /**
     * Export users report to Excel
     */
    public function exportUsersExcel(Request $request)
    {
        $startDate = Carbon::parse($request->get('start_date', now()->startOfMonth()));
        $endDate = Carbon::parse($request->get('end_date', now()->endOfMonth()));

        ActivityLog::log('exported', 'Exported users report to Excel', null, null, [
            'start_date' => $startDate->format('Y-m-d'),
            'end_date' => $endDate->format('Y-m-d')
        ]);

        return Excel::download(
            new UsersReportExport($startDate, $endDate),
            'users-report-' . now()->format('Y-m-d') . '.xlsx'
        );
    }

    /**
     * Export check-ins report to Excel
     */
    public function exportCheckinsExcel(Request $request)
    {
        $startDate = Carbon::parse($request->get('start_date', now()->startOfMonth()));
        $endDate = Carbon::parse($request->get('end_date', now()->endOfMonth()));

        ActivityLog::log('exported', 'Exported check-ins report to Excel', null, null, [
            'start_date' => $startDate->format('Y-m-d'),
            'end_date' => $endDate->format('Y-m-d')
        ]);

        return Excel::download(
            new CheckinsReportExport($startDate, $endDate),
            'checkins-report-' . now()->format('Y-m-d') . '.xlsx'
        );
    }
}
