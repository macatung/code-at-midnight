<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ApiProjectController extends Controller
{
    public function index(Request $request)
    {
        $query = Project::withCount('tasks');

        if ($request->has('type') && in_array($request->query('type'), ['work', 'personal'])) {
            $query->where('type', $request->query('type'));
        }

        $projects = $query->orderBy('title', 'asc')->get();

        return response()->json([
            'success' => true,
            'data' => $projects,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'type' => 'required|in:work,personal',
            'color' => 'nullable|string|max:50',
            'description' => 'nullable|string',
        ]);

        $validated['slug'] = Str::slug($validated['title']) . '-' . Str::random(5);
        $validated['category'] = $validated['type'] === 'work' ? 'web' : 'creative';
        $validated['color'] = $validated['color'] ?? ($validated['type'] === 'work' ? '#00f5a0' : '#ffd166');

        $project = Project::create($validated);
        $project->tasks_count = 0;

        return response()->json([
            'success' => true,
            'message' => 'Dự án đã được tạo thành công',
            'data' => $project,
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $project = Project::withCount('tasks')->findOrFail($id);

        $validated = $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'type' => 'sometimes|required|in:work,personal',
            'color' => 'nullable|string|max:50',
            'description' => 'nullable|string',
        ]);

        if (isset($validated['title']) && $validated['title'] !== $project->title) {
            $validated['slug'] = Str::slug($validated['title']) . '-' . Str::random(4);
        }

        $project->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Dự án đã được cập nhật',
            'data' => $project,
        ]);
    }

    public function destroy($id)
    {
        $project = Project::findOrFail($id);
        
        // Tasks have project_id nullOnDelete, so all tasks are safely preserved as Unassigned
        $project->delete();

        return response()->json([
            'success' => true,
            'message' => 'Dự án đã được xóa (Các nhiệm vụ liên quan đã được chuyển an toàn sang mục Chung)',
        ]);
    }
}
