<?php

// app/Http/Controllers/TimesheetController.php
namespace App\Http\Controllers;

use App\Models\WorkEntry;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\TimesheetExport;

class TimesheetController extends Controller
{
    /**
     * Show the timesheet page
     */
    public function index()
    {
        return view('timesheet');
    }

    /**
     * Get all work entries
     */
    public function getEntries(): JsonResponse
    {
        $entries = WorkEntry::orderBy('work_date', 'desc')
                           ->orderBy('start_time', 'desc')
                           ->get();

        return response()->json([
            'success' => true,
            'data' => $entries
        ]);
    }

    /**
     * Store a new work entry
     */
    public function storeEntry(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'work_date' => 'required|date',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'task_description' => 'required|string|max:1000',
        ]);

        // Calculate duration
        $startDateTime = Carbon::parse($validated['work_date'] . ' ' . $validated['start_time']);
        $endDateTime = Carbon::parse($validated['work_date'] . ' ' . $validated['end_time']);
        $duration = $endDateTime->diffInMinutes($startDateTime) / 60; // Convert to hours

        $entry = WorkEntry::create([
            'work_date' => $validated['work_date'],
            'start_time' => $validated['start_time'],
            'end_time' => $validated['end_time'],
            'task_description' => $validated['task_description'],
            'duration' => round($duration, 2)
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Work entry created successfully',
            'data' => $entry
        ]);
    }

    /**
     * Update a work entry
     */
    public function updateEntry(Request $request, WorkEntry $entry): JsonResponse
    {
        $validated = $request->validate([
            'work_date' => 'required|date',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'task_description' => 'required|string|max:1000',
        ]);

        // Calculate duration
        $startDateTime = Carbon::parse($validated['work_date'] . ' ' . $validated['start_time']);
        $endDateTime = Carbon::parse($validated['work_date'] . ' ' . $validated['end_time']);
        $duration = $endDateTime->diffInMinutes($startDateTime) / 60;

        $entry->update([
            'work_date' => $validated['work_date'],
            'start_time' => $validated['start_time'],
            'end_time' => $validated['end_time'],
            'task_description' => $validated['task_description'],
            'duration' => round($duration, 2)
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Work entry updated successfully',
            'data' => $entry
        ]);
    }

    /**
     * Delete a work entry
     */
    public function deleteEntry(WorkEntry $entry): JsonResponse
    {
        $entry->delete();

        return response()->json([
            'success' => true,
            'message' => 'Work entry deleted successfully'
        ]);
    }

    /**
     * Get 2-week report
     */
    public function getTwoWeekReport(Request $request): JsonResponse
    {
        $request->validate([
            'end_date' => 'required|date'
        ]);

        $endDate = $request->end_date;
        $entries = WorkEntry::twoWeekPeriod($endDate)
                           ->orderBy('work_date')
                           ->orderBy('start_time')
                           ->get();

        $totalHours = WorkEntry::calculateTotalHours($entries);
        $startDate = Carbon::parse($endDate)->subDays(13)->format('Y-m-d');

        return response()->json([
            'success' => true,
            'data' => [
                'entries' => $entries,
                'total_hours' => $totalHours,
                'start_date' => $startDate,
                'end_date' => $endDate,
                'period_summary' => [
                    'total_entries' => $entries->count(),
                    'total_hours' => $totalHours,
                    'average_hours_per_day' => $entries->count() > 0 ? round($totalHours / $entries->groupBy('work_date')->count(), 2) : 0
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
            'end_date' => 'required|date'
        ]);

        $endDate = $request->end_date;
        $entries = WorkEntry::twoWeekPeriod($endDate)
                           ->orderBy('work_date')
                           ->orderBy('start_time')
                           ->get();

        $startDate = Carbon::parse($endDate)->subDays(13)->format('Y-m-d');
        $filename = "Timesheet_{$startDate}_to_{$endDate}.xlsx";

        return Excel::download(new TimesheetExport($entries, $startDate, $endDate), $filename);
    }
}

