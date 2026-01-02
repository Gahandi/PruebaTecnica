<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class ActivityLogController extends Controller
{
    /**
     * Display a listing of activity logs
     */
    public function index(Request $request)
    {
        $query = ActivityLog::with('user');

        // Filter by user
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // Filter by action
        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        // Filter by model type
        if ($request->filled('model_type')) {
            $query->where('model_type', $request->model_type);
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Search in description
        if ($request->filled('search')) {
            $query->where('description', 'like', "%{$request->search}%");
        }

        // Order
        $query->latest();

        $logs = $query->paginate(50);

        // Get filter options
        $users = User::select('id', 'name', 'email')->get();
        $actions = ActivityLog::select('action')->distinct()->pluck('action');
        $modelTypes = ActivityLog::select('model_type')->distinct()->whereNotNull('model_type')->pluck('model_type');

        // Stats
        $stats = [
            'total' => ActivityLog::count(),
            'today' => ActivityLog::whereDate('created_at', today())->count(),
            'this_week' => ActivityLog::where('created_at', '>=', now()->startOfWeek())->count(),
            'this_month' => ActivityLog::whereMonth('created_at', now()->month)->count(),
        ];

        // Recent actions breakdown
        $actionBreakdown = ActivityLog::select('action', \DB::raw('count(*) as count'))
            ->where('created_at', '>=', now()->subDays(7))
            ->groupBy('action')
            ->get()
            ->pluck('count', 'action');

        return view('admin.activity-log.index', compact('logs', 'users', 'actions', 'modelTypes', 'stats', 'actionBreakdown'));
    }

    /**
     * Display the specified activity log
     */
    public function show(ActivityLog $activityLog)
    {
        $activityLog->load('user');

        return view('admin.activity-log.show', compact('activityLog'));
    }

    /**
     * Export activity logs
     */
    public function export(Request $request)
    {
        $query = ActivityLog::with('user');

        // Apply same filters as index
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }
        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }
        if ($request->filled('model_type')) {
            $query->where('model_type', $request->model_type);
        }
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $logs = $query->latest()->get();

        // Generate CSV
        $filename = 'activity_logs_' . now()->format('Y-m-d_His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($logs) {
            $file = fopen('php://output', 'w');

            // Headers
            fputcsv($file, ['ID', 'Usuario', 'Email', 'Acción', 'Acción (Código)', 'Descripción', 'Modelo', 'ID Modelo', 'IP', 'País', 'Ciudad', 'Fecha']);

            // Data
            foreach ($logs as $log) {
                fputcsv($file, [
                    $log->id,
                    $log->user ? $log->user->name : 'Sistema',
                    $log->user ? $log->user->email : '',
                    $log->action_translated,
                    $log->action,
                    $log->description,
                    $log->model_translated ?? '',
                    $log->model_id ?? '',
                    $log->ip_address ?? '',
                    $log->country ?? '',
                    $log->city ?? '',
                    $log->created_at->format('Y-m-d H:i:s'),
                ]);
            }

            fclose($file);
        };

        ActivityLog::log('exported', 'Exported activity logs', null, null, [
            'count' => $logs->count()
        ]);

        return Response::stream($callback, 200, $headers);
    }

    /**
     * Clear old logs
     */
    public function clear(Request $request)
    {
        $request->validate([
            'days' => ['required', 'integer', 'min:1', 'max:365']
        ]);

        $date = now()->subDays($request->days);
        $count = ActivityLog::where('created_at', '<', $date)->count();

        ActivityLog::where('created_at', '<', $date)->delete();

        ActivityLog::log('deleted', "Cleared activity logs older than {$request->days} days", null, null, [
            'days' => $request->days,
            'count' => $count
        ]);

        return redirect()->back()
            ->with('success', "Se eliminaron {$count} registros de actividad.");
    }
}
