<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\Project;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Carbon\Carbon;

class TaskController extends Controller
{
    public function index(Request $request)
    {
        $date = $request->query("date", Carbon::today()->toDateString());
        $projectId = $request->query("project_id");
        
        $query = Task::with('project');

        if ($projectId && $projectId !== 'all') {
            if ($projectId === 'unassigned') {
                $query->whereNull('project_id');
            } else {
                $query->where('project_id', $projectId);
            }
        }

        $tasks = $query->orderByRaw("CASE WHEN status = \"in_progress\" THEN 1 WHEN status = \"todo\" THEN 2 WHEN status = \"review\" THEN 3 ELSE 4 END")
            ->orderByRaw("CASE WHEN priority = \"urgent\" THEN 1 WHEN priority = \"high\" THEN 2 WHEN priority = \"medium\" THEN 3 ELSE 4 END")
            ->orderBy("created_at", "desc")
            ->get();

        $projects = Project::select('id', 'title', 'slug', 'category', 'type', 'color')
            ->withCount('tasks')
            ->orderBy('title')
            ->get();

        $stats = [
            "total" => $tasks->count(),
            "todo" => $tasks->where("status", "todo")->count(),
            "in_progress" => $tasks->where("status", "in_progress")->count(),
            "review" => $tasks->where("status", "review")->count(),
            "done" => $tasks->where("status", "done")->count(),
            "total_pomodoros_estimated" => $tasks->sum("estimated_pomodoros"),
            "total_pomodoros_completed" => $tasks->sum("completed_pomodoros"),
            "completion_rate" => $tasks->count() > 0 ? round(($tasks->where("status", "done")->count() / $tasks->count()) * 100) : 0,
        ];

        return Inertia::render("Tasks/Index", [
            "tasks" => $tasks,
            "projects" => $projects,
            "stats" => $stats,
            "selectedDate" => $date,
            "selectedProjectId" => $projectId ?: 'all',
        ]);
    }
}
