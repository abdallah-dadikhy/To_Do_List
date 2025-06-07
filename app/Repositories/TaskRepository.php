<?php

namespace App\Repositories;

use App\Interfaces\TaskRepositoryInterface;
use App\Models\Task;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Log;

class TaskRepository implements TaskRepositoryInterface
{
    public function getAllTasks(int $userId, array $filters = [], int $perPage = 10): LengthAwarePaginator
    {
        $tasks = Task::where('user_id', $userId)->with(['category', 'priority']);

        if (isset($filters['is_completed'])) {
            $tasks->where('is_completed', (bool)$filters['is_completed']);
        }

        if (isset($filters['priority_id'])) {
            $tasks->where('priority_id', $filters['priority_id']);
        }

        if (isset($filters['category_id'])) {
            $tasks->where('category_id', $filters['category_id']);
        }

        if (isset($filters['search'])) {
            $searchTerm = '%' . $filters['search'] . '%';
            $tasks->where(function ($query) use ($searchTerm) {
                $query->where('title', 'like', $searchTerm)
                      ->orWhere('description', 'like', $searchTerm);
            });
        }

        return $tasks->paginate($perPage);
    }

    public function getTaskById(int $taskId, int $userId): ?Task
    {
        return Task::where('id', $taskId)->where('user_id', $userId)->first();
    }

    public function createTask(array $taskDetails, int $userId): Task
    {
        $taskDetails['user_id'] = $userId;
        $task = Task::create($taskDetails);
        return $task->load(['category', 'priority']);
    }

    public function updateTask(int $taskId, int $userId, array $newDetails): ?Task
    {
        $task = $this->getTaskById($taskId, $userId);
        if ($task) {
            $task->update($newDetails);
            return $task->load(['category', 'priority']);
        }
        return null;
    }

    public function deleteTask(int $taskId, int $userId): bool
    {
        $task = $this->getTaskById($taskId, $userId);
        if ($task) {
            try {
                $task->delete();
                return true;
            } catch (\Exception $e) {
                Log::error("Failed to delete task {$taskId} for user {$userId}: " . $e->getMessage());
                return false;
            }
        }
        return false;
    }

    public function updateTaskCompletion(int $taskId, int $userId, bool $isCompleted): ?Task
    {
        $task = $this->getTaskById($taskId, $userId);
        if ($task) {
            $task->is_completed = $isCompleted;
            $task->save();
            return $task->load(['category', 'priority']);
        }
        return null;
    }
}