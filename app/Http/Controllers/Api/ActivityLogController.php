<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    /**
     * Get paginated activity logs with filters
     */
    public function index(Request $request)
    {
        $perPage = $request->get('per_page', 15);
        $action = $request->get('action');
        $userId = $request->get('user_id');
        $search = $request->get('search');

        $query = ActivityLog::with('user')
            ->latest();

        // Filter by action type
        if ($action && $action !== 'all') {
            $query->where('action', $action);
        }

        // Filter by user
        if ($userId && $userId !== 'all') {
            $query->where('user_id', $userId);
        }

        // Search in description using FULLTEXT index
        // Falls back to LIKE for very short queries (< 3 chars)
        if ($search) {
            if (strlen($search) < 3) {
                $query->where('description', 'like', "%{$search}%");
            } else {
                $query->whereFullText('description', $search);
            }
        }

        $logs = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $logs->map(function ($log) {
                return [
                    'id' => $log->id,
                    'user_name' => $log->user_name ?? 'System',
                    'user_id' => $log->user_id,
                    'action' => $log->action,
                    'description' => $log->description,
                    'model_type' => $log->model_type ? class_basename($log->model_type) : null,
                    'model_id' => $log->model_id,
                    'ip_address' => $log->ip_address,
                    'created_at' => $log->created_at->setTimezone('Asia/Jakarta')->format('d M Y H:i'),
                    'time_ago' => $log->created_at->diffForHumans(),
                    'has_changes' => $log->old_values || $log->new_values,
                    'old_values' => $log->old_values,
                    'new_values' => $log->new_values,
                ];
            }),
            'meta' => [
                'current_page' => $logs->currentPage(),
                'last_page' => $logs->lastPage(),
                'per_page' => $logs->perPage(),
                'total' => $logs->total(),
            ]
        ]);
    }

    /**
     * Get available action types for filter dropdown
     */
    public function actions()
    {
        $actions = ActivityLog::distinct()
            ->pluck('action')
            ->filter()
            ->values();

        return response()->json([
            'success' => true,
            'data' => $actions
        ]);
    }

    /**
     * Get users who have activity for filter dropdown
     */
    public function users()
    {
        $users = ActivityLog::select('user_id', 'user_name')
            ->distinct()
            ->whereNotNull('user_id')
            ->get()
            ->unique('user_id')
            ->values();

        return response()->json([
            'success' => true,
            'data' => $users
        ]);
    }
}
