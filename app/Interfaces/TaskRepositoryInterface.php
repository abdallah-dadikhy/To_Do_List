<?php

namespace App\Interfaces;

use App\Models\Task;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface TaskRepositoryInterface
{
    public function getAllTasks(int $userId, array $filters = [], int $perPage = 10): LengthAwarePaginator;
    public function getTaskById(int $taskId, int $userId): ?Task;
    public function createTask(array $taskDetails, int $userId): Task;
    public function updateTask(int $taskId, int $userId, array $newDetails): ?Task;
    public function deleteTask(int $taskId, int $userId): bool;
    public function updateTaskCompletion(int $taskId, int $userId, bool $isCompleted): ?Task;
}