<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTaskRequest;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\JsonResponse;

class TaskController extends Controller
{

    public function index(): JsonResponse
    {
        $tasks = Task::where('user_id', Auth::id())
            ->orderByRaw('ISNULL(due_date), due_date ASC')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($tasks);
    }

    public function store(StoreTaskRequest $request): JsonResponse // Use StoreTaskRequest
    {

        $validatedData = $request->validated();

        $task = Task::create([
            'user_id' => Auth::id(),
            'title' => $validatedData['title'],
            'description' => $validatedData['description'] ?? null,
            'due_date' => $validatedData['due_date'] ?? null,
            'status' => 'pending',
        ]);

        return response()->json($task, 201);
    }

    public function update(Request $request, Task $task): JsonResponse
    {

        if ($task->user_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validatedData = $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'sometimes|required|in:pending,in_progress,completed',
            'due_date' => ['nullable', 'date_format:Y-m-d', 'after_or_equal:today'],
        ]);


        $task->update($validatedData);

        return response()->json($task);
    }

    public function destroy(Task $task): JsonResponse
    {
        if ($task->user_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $task->delete();

        return response()->json(['message' => 'Task deleted successfully']);
    }

    public function toggleStatus(Task $task): JsonResponse
    {
        if ($task->user_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $task->status = $task->status === 'pending' ? 'completed' : 'pending';
        $task->save();

        return response()->json(['message' => 'Task status updated successfully', 'task' => $task]);
    }
}
