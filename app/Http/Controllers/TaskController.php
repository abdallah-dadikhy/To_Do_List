<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class TaskController extends Controller
{

    public function index(Request $request)
    {
        $user = Auth::user();
        $tasks = $user->tasks(); 


        if ($request->has('is_completed')) {
            $tasks->where('is_completed', (bool)$request->input('is_completed'));
        }

        if ($request->has('priority_id')) {
            $tasks->where('priority_id', $request->input('priority_id'));
        }

        if ($request->has('category_id')) {
            $tasks->where('category_id', $request->input('category_id'));
        }

        if ($request->has('search')) {
            $searchTerm = '%' . $request->input('search') . '%';
            $tasks->where(function ($query) use ($searchTerm) {
                $query->where('title', 'like', $searchTerm)
                      ->orWhere('description', 'like', $searchTerm);
            });
        }

        $perPage = $request->input('per_page', 10); 
        $tasks = $tasks->with(['category', 'priority'])->paginate($perPage);

        return response()->json($tasks);
    }


    public function show(Task $task)
    {
        if ($task->user_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized to view this task.'], 403);
        }

        return response()->json($task->load(['category', 'priority']));
    }

    public function store(Request $request)
    {
$user = Auth::user();

        if ($user->role !== 'owner') {
            return response()->json(['message' => 'Only owners can create tasks.'], 403);
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'due_date' => 'nullable|date',
            'priority_id' => 'nullable|exists:priorities,id',
            'category_id' => 'nullable|exists:categories,id',
        ]);

        $task = $user->tasks()->create($request->all());

        return response()->json([
            'message' => 'Task created successfully',
            'task' => $task->load(['category', 'priority'])
        ], 201);
    }

public function update(Request $request, $id)
{
    $task = Task::find($id);

    if (!$task) {
        return response()->json(['message' => 'Task not found'], 404);
    }
        $task->is_completed = $request->is_completed;
        $task->save();

    $user = Auth::user();

    if ($user->role === 'guest') {
        $request->validate([
            'is_completed' => 'required|boolean',
        ]);

        $task->is_completed = $request->is_completed;
        $task->save();

        $task->load('category', 'priority');

        return response()->json([
            'message' => 'Task status updated successfully',
            'task' => [
                'id' => $task->id,
                'title' => $task->title,
                'description' => $task->description,
                'is_completed' => $task->is_completed,
                'due_date' => $task->due_date,
                'category' => $task->category?->name,
                'priority' => $task->priority?->name,
            ]
        ]);
    }

    if ($user->role === 'owner') {
        $validated = $request->validate([
            'title' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'is_completed' => 'sometimes|boolean',
            'due_date' => 'nullable|date',
            'priority_id' => 'nullable|exists:priorities,id',
            'category_id' => 'nullable|exists:categories,id',
        ]);

        $task->update($validated);

        $task->load('category', 'priority');

        return response()->json([
            'message' => 'Task updated successfully',
            'task' => [
                'id' => $task->id,
                'title' => $task->title,
                'description' => $task->description,
                'is_completed' => $task->is_completed,
                'due_date' => $task->due_date,
                'category' => $task->category?->name,
                'priority' => $task->priority?->name,
                'created_at' => $task->created_at,
                'updated_at' => $task->updated_at,
            ]
        ]);
    }

    return response()->json(['message' => 'Unauthorized action.'], 403);
}

    public function destroy(Task $task)
    {
         $user = Auth::user();

        if ($task->user_id !== $user->id) {
            return response()->json(['message' => 'Unauthorized to delete this task.'], 403);
        }

        if ($user->role !== 'owner') {
            return response()->json(['message' => 'Only owners can delete tasks.'], 403);
        }
        $task->delete();
        return response()->json(['message' => 'Task deleted successfully'], 204);
    }
}