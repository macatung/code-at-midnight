<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Task;
use App\Models\Project;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ApiTaskController extends Controller
{
    public function index(Request $request)
    {
        $query = Task::with('project');

        if ($request->has("project_id") && $request->query("project_id") !== 'all') {
            $query->where("project_id", $request->query("project_id"));
        }

        if ($request->has("status")) {
            $query->where("status", $request->query("status"));
        }

        if ($request->has("today")) {
            $query->where(function ($q) {
                $q->whereDate("due_date", Carbon::today())
                  ->orWhereNull("due_date")
                  ->orWhere("status", "in_progress");
            });
        }

        $tasks = $query->orderByRaw("CASE WHEN status = \"in_progress\" THEN 1 WHEN status = \"todo\" THEN 2 WHEN status = \"review\" THEN 3 ELSE 4 END")
            ->orderByRaw("CASE WHEN priority = \"urgent\" THEN 1 WHEN priority = \"high\" THEN 2 WHEN priority = \"medium\" THEN 3 ELSE 4 END")
            ->orderBy("created_at", "desc")
            ->get();

        return response()->json([
            "success" => true,
            "data" => $tasks,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            "project_id" => "nullable|exists:projects,id",
            "title" => "required|string|max:255",
            "description" => "nullable|string",
            "status" => "nullable|in:todo,in_progress,review,done",
            "priority" => "nullable|in:urgent,high,medium,low",
            "category" => "nullable|string",
            "estimated_pomodoros" => "nullable|integer|min:1|max:20",
            "due_date" => "nullable|date",
            "notes" => "nullable|string",
        ]);

        if (empty($validated["due_date"])) {
            $validated["due_date"] = Carbon::today()->toDateString();
        }

        $task = Task::create($validated);
        $task->load('project');

        return response()->json([
            "success" => true,
            "message" => "Task created successfully",
            "data" => $task,
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $task = Task::findOrFail($id);

        $validated = $request->validate([
            "project_id" => "nullable|exists:projects,id",
            "title" => "sometimes|string|max:255",
            "description" => "nullable|string",
            "status" => "sometimes|in:todo,in_progress,review,done",
            "priority" => "sometimes|in:urgent,high,medium,low",
            "category" => "sometimes|string",
            "estimated_pomodoros" => "sometimes|integer|min:1|max:20",
            "completed_pomodoros" => "sometimes|integer|min:0",
            "due_date" => "nullable|date",
            "notes" => "nullable|string",
        ]);

        if (isset($validated["status"]) && $validated["status"] === "done" && $task->status !== "done") {
            $validated["completed_at"] = Carbon::now();
        } elseif (isset($validated["status"]) && $validated["status"] !== "done") {
            $validated["completed_at"] = null;
        }

        $task->update($validated);
        $task->load('project');

        return response()->json([
            "success" => true,
            "message" => "Task updated successfully",
            "data" => $task,
        ]);
    }

    public function destroy($id)
    {
        $task = Task::findOrFail($id);
        $task->delete();

        return response()->json([
            "success" => true,
            "message" => "Task deleted successfully",
        ]);
    }

    // Daily Quest Dispatch for Mascot
    public function dailyDispatch()
    {
        $todayTasks = Task::with('project')
            ->where(function ($q) {
                $q->whereDate("due_date", Carbon::today())
                  ->orWhereNull("due_date")
                  ->orWhere("status", "in_progress");
            })
            ->where("status", "!=", "done")
            ->orderByRaw("CASE WHEN status = \"in_progress\" THEN 1 ELSE 2 END")
            ->orderByRaw("CASE WHEN priority = \"urgent\" THEN 1 WHEN priority = \"high\" THEN 2 WHEN priority = \"medium\" THEN 3 ELSE 4 END")
            ->take(5)
            ->get();

        $completedToday = Task::where("status", "done")
            ->whereDate("completed_at", Carbon::today())
            ->count();

        return response()->json([
            "success" => true,
            "dispatch_date" => Carbon::today()->toDateString(),
            "active_tasks" => $todayTasks,
            "completed_today_count" => $completedToday,
            "greeting" => "Chào buổi sáng! Hãy cùng Ma Cà Tưng chinh phục các nhiệm vụ trọng tâm hôm nay 🚀",
        ]);
    }

    // Daily Retrospective / Review for Mascot
    public function dailyReview()
    {
        $today = Carbon::today();

        $completedTasks = Task::with('project')
            ->where("status", "done")
            ->whereDate("completed_at", $today)
            ->get();

        $incompletedTasks = Task::with('project')
            ->where("status", "!=", "done")
            ->where(function ($q) use ($today) {
                $q->whereDate("due_date", $today)
                  ->orWhere("status", "in_progress");
            })
            ->get();

        $totalPomodorosDone = Task::whereDate("updated_at", $today)->sum("completed_pomodoros");

        return response()->json([
            "success" => true,
            "review_date" => $today->toDateString(),
            "completed_count" => $completedTasks->count(),
            "incompleted_count" => $incompletedTasks->count(),
            "total_pomodoros_done" => $totalPomodorosDone,
            "completed_tasks" => $completedTasks,
            "incompleted_tasks" => $incompletedTasks,
            "wisdom_quote" => "Chiến thắng vĩ đại nhất của bậc trượng phu là tự thắng sự lười biếng và giữ tâm bất động trước nghịch cảnh.",
        ]);
    }
}
