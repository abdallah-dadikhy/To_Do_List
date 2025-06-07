<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Task;
use Illuminate\Auth\Access\Response;

class TaskPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        // Owner و Guest يمكنهم رؤية جميع مهامهم الخاصة
        return $user->role === 'owner' || $user->role === 'guest';
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Task $task): bool
    {
        // يمكن للمالك والضيف رؤية مهمتهم الخاصة
        return ($user->role === 'owner' || $user->role === 'guest') && $user->id === $task->user_id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // Owner و Guest يمكنهم إنشاء المهام
        return $user->role === 'owner' || $user->role === 'guest';
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Task $task): bool
    {
        // المالك يمكنه تحديث أي مهمة
        // الضيف يمكنه تحديث مهمته الخاصة فقط
        return $user->role === 'owner' || ($user->role === 'guest' && $user->id === $task->user_id);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Task $task): bool
    {
        // المالك يمكنه حذف أي مهمة
        // الضيف يمكنه حذف مهمته الخاصة فقط
        return $user->role === 'owner' || ($user->role === 'guest' && $user->id === $task->user_id);
    }

    /**
     * Determine whether the user can mark a task as completed/uncompleted.
     * (هذه وظيفة لم تكن Policy لها بشكل افتراضي، ولكنها منطقية لإضافتها هنا)
     */
    public function complete(User $user, Task $task): bool
    {
        // المالك يمكنه تغيير حالة أي مهمة
        // الضيف يمكنه تغيير حالة مهمته الخاصة فقط
        return $user->role === 'owner' || ($user->role === 'guest' && $user->id === $task->user_id);
    }

    /**
     * Determine whether the user can invite other users (Owner specific).
     */
    public function inviteUsers(User $user): bool
    {
        return $user->role === 'owner';
    }
}