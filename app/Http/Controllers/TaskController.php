<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TaskController extends Controller
{
    public function index()
    {
        $tasks = Task::where('user_id', Auth::id())
            ->orderBy('due_date', 'asc')
            ->orderBy('priority', 'desc')
            ->get();

        return view('pages.tasks.index', compact('tasks'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'due_date' => 'nullable|date',
            'priority' => 'nullable|in:low,medium,high',
        ]);

        $validated['user_id'] = Auth::id();
        $validated['is_completed'] = false;
        $validated['priority'] = match ($validated['priority'] ?? 'medium') {
            'low' => 1,
            'high' => 3,
            default => 2,
        };

        Task::create($validated);

        return back()->with('success', __('lms.task_created'));
    }

    public function update(Request $request, Task $task)
    {
        if ($task->user_id !== Auth::id()) {
            abort(403);
        }

        $validated = $request->validate([
            'title' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'due_date' => 'nullable|date',
            'priority' => 'nullable|in:low,medium,high',
            'is_completed' => 'nullable',
        ]);

        // Convert priority string to integer if provided
        if (isset($validated['priority'])) {
            $validated['priority'] = match ($validated['priority']) {
                'low' => 1,
                'high' => 3,
                default => 2,
            };
        }

        // Handle is_completed checkbox (unchecked = not sent)
        $validated['is_completed'] = $request->has('is_completed');

        $task->update($validated);

        return back()->with('success', __('lms.task_updated'));
    }

    public function toggleComplete(Task $task)
    {
        if ($task->user_id !== Auth::id()) {
            abort(403);
        }

        $newStatus = ! $task->is_completed;
        $task->update([
            'is_completed' => $newStatus,
            'completed_at' => $newStatus ? now() : null,
        ]);

        return back()->with('success', $newStatus ? __('lms.task_completed') : __('lms.task_marked_incomplete'));
    }

    public function destroy(Task $task)
    {
        if ($task->user_id !== Auth::id()) {
            abort(403);
        }

        $task->delete();

        return back()->with('success', __('lms.task_deleted'));
    }
}
