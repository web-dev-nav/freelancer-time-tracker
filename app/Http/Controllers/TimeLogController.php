<?php


// app/Http/Controllers/TimeLogController.php

namespace App\Http\Controllers;

use App\Models\TimeLog;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\TimeLogExport;

class TimeLogController extends Controller
{
    /**
     * Display the timesheet interface
     */
    public function index()
    {
        $activeSession = TimeLog::getActiveSession();
        
        return view('timesheet.index', [
            'activeSession' => $activeSession
        ]);
    }

    /**
     * Clock in - Start a new work session
     */
    public function clockIn(Request $request)
    {
        $request->validate([
            'date' => 'required|date',
            'time' => 'required|date_format:H:i'
        ]);

        // Check for existing active session
        $existingSession = TimeLog::getActiveSession();
        if ($existingSession) {
            return response()->json([
                'success' => false,
                'message' => 'You already have an active session. Please clock out first.',
                'active_session' => $existingSession
            ], 422);
        }

        // Create clock-in datetime in Toronto timezone
        $clockIn = Carbon::createFromFormat('Y-m-d H:i', $request->date . ' ' . $request->time, 'America/Toronto')
                        ->setTimezone('UTC'); // Convert to UTC for storage

        // Create new session
        $session = TimeLog::createSession(
            $clockIn,
            $request->ip(),
            $request->userAgent()
        );

        return response()->json([
            'success' => true,
            'message' => 'Successfully clocked in!',
            'session' => $session
        ]);
    }

    /**
     * Clock out - End the current work session
     */
    public function clockOut(Request $request)
    {
        $request->validate([
            'session_id' => 'required|exists:time_logs,session_id',
            'time' => 'required|date_format:H:i',
            'work_description' => 'required|string|max:1000'
        ]);

        $session = TimeLog::where('session_id', $request->session_id)->first();

        if (!$session || $session->status !== 'active') {
            return response()->json([
                'success' => false,
                'message' => 'No active session found'
            ], 404);
        }

        // Create clock-out datetime in Toronto timezone
        $clockOutDate = $session->clock_in->copy()->setTimezone('America/Toronto')->format('Y-m-d');
        $clockOut = Carbon::createFromFormat('Y-m-d H:i', $clockOutDate . ' ' . $request->time, 'America/Toronto')
                         ->setTimezone('UTC'); // Convert to UTC for storage

        // Handle overnight sessions
        if ($clockOut->lt($session->clock_in)) {
            $clockOut->addDay();
        }

        // Validate duration
        $totalMinutes = $session->clock_in->diffInMinutes($clockOut);
        if ($totalMinutes <= 0) {
            return response()->json([
                'success' => false,
                'message' => 'Clock out time must be after clock in time'
            ], 422);
        }

        if ($totalMinutes > (24 * 60)) {
            return response()->json([
                'success' => false,
                'message' => 'Session cannot exceed 24 hours'
            ], 422);
        }

        // Complete the session
        $session->completeSession(
            $clockOut,
            $request->work_description,
            null
        );

        return response()->json([
            'success' => true,
            'message' => 'Successfully clocked out!',
            'session' => $session->fresh()
        ]);
    }

    /**
     * Cancel active session
     */
    public function cancelSession(Request $request)
    {
        $session = TimeLog::getActiveSession();

        if (!$session) {
            return response()->json([
                'success' => false,
                'message' => 'No active session found'
            ], 404);
        }

        $session->cancelSession();

        return response()->json([
            'success' => true,
            'message' => 'Session cancelled successfully'
        ]);
    }

    /**
     * Get current active session
     */
    public function getActiveSession()
    {
        $session = TimeLog::getActiveSession();

        if ($session) {
            return response()->json([
                'success' => true,
                'session' => $session
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'No active session'
        ], 404);
    }

    /**
     * Get work history with pagination
     */
    public function getHistory(Request $request)
    {
        $perPage = $request->get('per_page', 15);
        $page = $request->get('page', 1);
        
        $logs = TimeLog::completed()
                       ->orderBy('clock_in', 'desc')
                       ->paginate($perPage, ['*'], 'page', $page);

        return response()->json([
            'success' => true,
            'data' => [
                'items' => $logs->items(),
                'pagination' => [
                    'current_page' => $logs->currentPage(),
                    'last_page' => $logs->lastPage(),
                    'per_page' => $logs->perPage(),
                    'total' => $logs->total(),
                    'from' => $logs->firstItem(),
                    'to' => $logs->lastItem(),
                    'has_more_pages' => $logs->hasMorePages(),
                    'prev_page_url' => $logs->previousPageUrl(),
                    'next_page_url' => $logs->nextPageUrl()
                ]
            ]
        ]);
    }

    /**
     * Get a single time log entry
     */
    public function getLog($id)
    {
        $log = TimeLog::findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $log
        ]);
    }

    /**
     * Delete a time log entry
     */
    public function deleteLog(Request $request, $id)
    {
        $log = TimeLog::findOrFail($id);
        $log->delete();

        return response()->json([
            'success' => true,
            'message' => 'Time log deleted successfully'
        ]);
    }

    /**
     * Update a time log entry
     */
    public function updateLog(Request $request, $id)
    {
        $request->validate([
            'date' => 'required|date',
            'clock_in_time' => 'required|date_format:H:i',
            'clock_out_time' => 'required|date_format:H:i',
            'work_description' => 'required|string|max:1000'
        ]);

        $log = TimeLog::findOrFail($id);

        // Create clock-in and clock-out datetimes in Toronto timezone, then convert to UTC
        $clockIn = Carbon::createFromFormat('Y-m-d H:i', $request->date . ' ' . $request->clock_in_time, 'America/Toronto')
                        ->setTimezone('UTC');
        $clockOut = Carbon::createFromFormat('Y-m-d H:i', $request->date . ' ' . $request->clock_out_time, 'America/Toronto')
                         ->setTimezone('UTC');

        // Handle overnight sessions
        if ($clockOut->lt($clockIn)) {
            $clockOut->addDay();
        }

        $totalMinutes = $clockIn->diffInMinutes($clockOut);

        if ($totalMinutes <= 0) {
            return response()->json([
                'success' => false,
                'message' => 'Clock out time must be after clock in time'
            ], 422);
        }

        if ($totalMinutes > (24 * 60)) {
            return response()->json([
                'success' => false,
                'message' => 'Session cannot exceed 24 hours'
            ], 422);
        }

        $log->update([
            'clock_in' => $clockIn,
            'clock_out' => $clockOut,
            'total_minutes' => $totalMinutes,
            'work_description' => $request->work_description,
            'project_name' => null
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Time log updated successfully',
            'data' => $log->fresh()
        ]);
    }

    /**
     * Generate custom date range report
     */
    public function generateReport(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date'
        ]);

        $startDate = $request->start_date;
        $endDate = $request->end_date;
        
        $logs = TimeLog::completed()
                       ->byDateRange($startDate, $endDate)
                       ->orderBy('clock_in')
                       ->get();
        $totalHours = TimeLog::calculateTotalHours($logs);
        $totalMinutes = TimeLog::calculateTotalMinutes($logs);

        // Group by date for better organization
        $groupedLogs = $logs->groupBy(function($log) {
            return $log->clock_in->format('Y-m-d');
        });

        return response()->json([
            'success' => true,
            'data' => [
                'logs' => $logs,
                'grouped_logs' => $groupedLogs,
                'period' => [
                    'start_date' => $startDate,
                    'end_date' => $endDate
                ],
                'summary' => [
                    'total_hours' => round($totalHours, 2),
                    'total_minutes' => $totalMinutes,
                    'total_sessions' => $logs->count(),
                    'average_hours_per_day' => $logs->count() > 0 ? round($totalHours / $groupedLogs->count(), 2) : 0,
                    'days_worked' => $groupedLogs->count()
                ]
            ]
        ]);
    }

    /**
     * Export timesheet to Excel
     */
    public function exportExcel(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date'
        ]);

        $startDate = $request->start_date;
        $endDate = $request->end_date;
        
        $logs = TimeLog::completed()
                       ->byDateRange($startDate, $endDate)
                       ->orderBy('clock_in')
                       ->get();

        if ($logs->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'No data found for the selected period'
            ], 404);
        }
        $filename = "Timesheet_{$startDate}_to_{$endDate}.xlsx";

        return Excel::download(new TimeLogExport($logs, $startDate, $endDate), $filename);
    }

    /**
     * Get dashboard statistics
     */
    public function getDashboardStats()
    {
        $today = Carbon::today();
        $thisWeek = Carbon::now()->startOfWeek();
        $thisMonth = Carbon::now()->startOfMonth();

        $stats = [
            'today' => [
                'hours' => TimeLog::completed()->byDateRange($today, $today)->sum('total_minutes') / 60,
                'sessions' => TimeLog::completed()->byDateRange($today, $today)->count()
            ],
            'this_week' => [
                'hours' => TimeLog::completed()->byDateRange($thisWeek, Carbon::now())->sum('total_minutes') / 60,
                'sessions' => TimeLog::completed()->byDateRange($thisWeek, Carbon::now())->count()
            ],
            'this_month' => [
                'hours' => TimeLog::completed()->byDateRange($thisMonth, Carbon::now())->sum('total_minutes') / 60,
                'sessions' => TimeLog::completed()->byDateRange($thisMonth, Carbon::now())->count()
            ],
            'active_session' => TimeLog::getActiveSession()
        ];

        return response()->json([
            'success' => true,
            'stats' => $stats
        ]);
    }
}