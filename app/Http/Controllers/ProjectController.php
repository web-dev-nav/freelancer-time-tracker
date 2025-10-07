<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    /**
     * Get all projects with optional filtering by status
     */
    public function index(Request $request)
    {
        $status = $request->get('status', 'active');

        $query = Project::query();

        if ($status === 'active') {
            $query->active();
        } elseif ($status === 'archived') {
            $query->archived();
        }

        $projects = $query->orderBy('name')->get();

        // Load stats for each project
        $projects->each(function ($project) {
            $project->loadCount('timeLogs');
            $project->setAttribute('total_hours', $project->total_hours);
            $project->setAttribute('total_sessions', $project->total_sessions);
            $project->setAttribute('total_earnings', $project->total_earnings);
        });

        return response()->json([
            'success' => true,
            'data' => $projects
        ]);
    }

    /**
     * Get active projects (for dropdowns)
     */
    public function active()
    {
        $projects = Project::active()->orderBy('name')->get(['id', 'name', 'client_name', 'color']);

        return response()->json([
            'success' => true,
            'data' => $projects
        ]);
    }

    /**
     * Get a single project with stats
     */
    public function show($id)
    {
        $project = Project::findOrFail($id);

        $project->loadCount('timeLogs');
        $project->setAttribute('total_hours', $project->total_hours);
        $project->setAttribute('total_sessions', $project->total_sessions);
        $project->setAttribute('total_earnings', $project->total_earnings);

        return response()->json([
            'success' => true,
            'data' => $project
        ]);
    }

    /**
     * Create a new project
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'client_name' => 'nullable|string|max:255',
            'color' => 'nullable|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'hourly_rate' => 'nullable|numeric|min:0',
            'description' => 'nullable|string|max:1000'
        ]);

        $project = Project::create([
            'name' => $request->name,
            'client_name' => $request->client_name,
            'color' => $request->color ?? '#8b5cf6',
            'hourly_rate' => $request->hourly_rate,
            'description' => $request->description,
            'status' => 'active'
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Project created successfully',
            'data' => $project
        ], 201);
    }

    /**
     * Update a project
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'client_name' => 'nullable|string|max:255',
            'color' => 'nullable|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'hourly_rate' => 'nullable|numeric|min:0',
            'description' => 'nullable|string|max:1000',
            'status' => 'nullable|in:active,archived'
        ]);

        $project = Project::findOrFail($id);

        $project->update([
            'name' => $request->name,
            'client_name' => $request->client_name,
            'color' => $request->color ?? $project->color,
            'hourly_rate' => $request->hourly_rate,
            'description' => $request->description,
            'status' => $request->status ?? $project->status
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Project updated successfully',
            'data' => $project->fresh()
        ]);
    }

    /**
     * Archive a project
     */
    public function archive($id)
    {
        $project = Project::findOrFail($id);
        $project->archive();

        return response()->json([
            'success' => true,
            'message' => 'Project archived successfully',
            'data' => $project
        ]);
    }

    /**
     * Activate a project
     */
    public function activate($id)
    {
        $project = Project::findOrFail($id);
        $project->activate();

        return response()->json([
            'success' => true,
            'message' => 'Project activated successfully',
            'data' => $project
        ]);
    }

    /**
     * Delete a project (only if no time logs)
     */
    public function destroy($id)
    {
        $project = Project::findOrFail($id);

        // Check if project has time logs
        if ($project->timeLogs()->count() > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete project with existing time logs. Archive it instead.'
            ], 422);
        }

        $project->delete();

        return response()->json([
            'success' => true,
            'message' => 'Project deleted successfully'
        ]);
    }

    /**
     * Get project statistics
     */
    public function stats($id)
    {
        $project = Project::findOrFail($id);

        $timeLogs = $project->timeLogs()->completed()->get();

        $stats = [
            'total_hours' => $project->total_hours,
            'total_minutes' => $project->total_minutes,
            'total_sessions' => $project->total_sessions,
            'total_earnings' => $project->total_earnings,
            'average_session_hours' => $project->total_sessions > 0
                ? round($project->total_hours / $project->total_sessions, 2)
                : 0,
            'recent_activity' => $timeLogs->take(5)->map(function ($log) {
                return [
                    'id' => $log->id,
                    'date' => $log->clock_in->format('Y-m-d'),
                    'hours' => $log->duration_hours,
                    'description' => $log->work_description
                ];
            })
        ];

        return response()->json([
            'success' => true,
            'data' => $stats
        ]);
    }
}
