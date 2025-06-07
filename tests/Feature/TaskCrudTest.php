<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Task;
use App\Models\Category;
use App\Models\Priority;
use Laravel\Sanctum\Sanctum;

class TaskCrudTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected User $ownerUser;
    protected User $guestUser;
    protected Category $category;
    protected Priority $priority;
    protected Task $ownerTask;
    protected Task $guestTask;
    protected Task $anotherOwnerTask;


    protected function setUp(): void
    {
        parent::setUp();
        // Seeders for base data
        $this->seed();

        $this->ownerUser = User::factory()->create(['role' => 'owner']);
        $this->guestUser = User::factory()->create(['role' => 'guest']);
        $this->category = Category::factory()->create();
        $this->priority = Priority::factory()->create();

        $this->ownerTask = Task::factory()->create([
            'user_id' => $this->ownerUser->id,
            'title' => 'Owner Task',
            'is_completed' => false,
            'category_id' => $this->category->id,
            'priority_id' => $this->priority->id,
        ]);
        $this->anotherOwnerTask = Task::factory()->create([
            'user_id' => $this->ownerUser->id,
            'title' => 'Another Owner Task',
            'is_completed' => false,
        ]);

        $this->guestTask = Task::factory()->create([
            'user_id' => $this->guestUser->id,
            'title' => 'Guest Task',
            'is_completed' => false,
        ]);
    }

    /** @test */
    public function owner_can_create_a_task()
    {
        Sanctum::actingAs($this->ownerUser, ['*']);

        $taskData = [
            'title' => 'New Task for Owner',
            'description' => 'Description for new task',
            'due_date' => '2025-07-01',
            'priority_id' => $this->priority->id,
            'category_id' => $this->category->id,
        ];

        $response = $this->postJson('/api/tasks', $taskData);

        $response->assertStatus(201)
                 ->assertJsonStructure(['message', 'task'])
                 ->assertJson([
                     'message' => 'Task created successfully',
                     'task' => ['title' => 'New Task for Owner', 'user_id' => $this->ownerUser->id]
                 ]);

        $this->assertDatabaseHas('tasks', ['title' => 'New Task for Owner', 'user_id' => $this->ownerUser->id]);
    }

    /** @test */
    public function guest_cannot_create_a_task()
    {
        Sanctum::actingAs($this->guestUser, ['*']);

        $taskData = [
            'title' => 'New Task for Guest',
        ];

        $response = $this->postJson('/api/tasks', $taskData);

        $response->assertStatus(403) // Forbidden
                 ->assertJson(['message' => 'Only owners can create tasks.']);
    }

    /** @test */
    public function owner_can_view_their_own_tasks()
    {
        Sanctum::actingAs($this->ownerUser, ['*']);

        $response = $this->getJson('/api/tasks');

        $response->assertOk()
                 ->assertJsonCount(2, 'data') // assuming only 2 tasks created for owner in setUp
                 ->assertJsonFragment(['title' => $this->ownerTask->title])
                 ->assertJsonFragment(['title' => $this->anotherOwnerTask->title])
                 ->assertJsonMissing(['title' => $this->guestTask->title]); // Should not see guest's task
    }

    /** @test */
    public function guest_can_view_their_own_tasks()
    {
        Sanctum::actingAs($this->guestUser, ['*']);

        $response = $this->getJson('/api/tasks');

        $response->assertOk()
                 ->assertJsonCount(1, 'data') // only 1 task created for guest in setUp
                 ->assertJsonFragment(['title' => $this->guestTask->title])
                 ->assertJsonMissing(['title' => $this->ownerTask->title]); // Should not see owner's task
    }

    /** @test */
    public function owner_can_view_a_specific_task_they_own()
    {
        Sanctum::actingAs($this->ownerUser, ['*']);

        $response = $this->getJson("/api/tasks/{$this->ownerTask->id}");

        $response->assertOk()
                 ->assertJson(['title' => $this->ownerTask->title]);
    }

    /** @test */
    public function owner_cannot_view_a_specific_task_they_dont_own()
    {
        Sanctum::actingAs($this->ownerUser, ['*']); // Owner is authenticated

        $response = $this->getJson("/api/tasks/{$this->guestTask->id}"); // Tries to view guest's task

        $response->assertStatus(403) // Forbidden
                 ->assertJson(['message' => 'Unauthorized to view this task.']);
    }

    /** @test */
    public function guest_cannot_view_a_specific_task_they_dont_own()
    {
        Sanctum::actingAs($this->guestUser, ['*']); // Guest is authenticated

        $response = $this->getJson("/api/tasks/{$this->ownerTask->id}"); // Tries to view owner's task

        $response->assertStatus(403) // Forbidden
                 ->assertJson(['message' => 'Unauthorized to view this task.']);
    }


    /** @test */
    public function owner_can_update_their_own_task_fully()
    {
        Sanctum::actingAs($this->ownerUser, ['*']);

        $updatedData = [
            'title' => 'Updated Title by Owner',
            'description' => 'Updated description.',
            'is_completed' => true,
            'due_date' => '2025-12-31',
            'priority_id' => Priority::factory()->create()->id,
            'category_id' => Category::factory()->create()->id,
        ];

        $response = $this->putJson("/api/tasks/{$this->ownerTask->id}", $updatedData);

        $response->assertOk()
                 ->assertJson([
                     'message' => 'Task updated successfully',
                     'task' => [
                         'id' => $this->ownerTask->id,
                         'title' => 'Updated Title by Owner',
                         'is_completed' => true,
                     ]
                 ]);

        $this->assertDatabaseHas('tasks', [
            'id' => $this->ownerTask->id,
            'title' => 'Updated Title by Owner',
            'is_completed' => true,
            'due_date' => '2025-12-31',
        ]);
    }

    /** @test */
    public function guest_can_only_update_is_completed_on_their_own_task()
    {
        Sanctum::actingAs($this->guestUser, ['*']);

        $updatedData = [
            'is_completed' => true,
            'title' => 'Trying to update title as guest', // This should be ignored
        ];

        $response = $this->putJson("/api/tasks/{$this->guestTask->id}", $updatedData);

        $response->assertOk()
                 ->assertJson([
                     'message' => 'Task status updated successfully',
                     'task' => [
                         'id' => $this->guestTask->id,
                         'is_completed' => true,
                     ]
                 ]);

        // تأكد أن فقط is_completed تم تحديثها، وأن العنوان لم يتغير
        $this->assertDatabaseHas('tasks', [
            'id' => $this->guestTask->id,
            'is_completed' => true,
            'title' => 'Guest Task', // العنوان لم يتغير
        ]);
    }

    /** @test */
    public function owner_can_delete_their_own_task()
    {
        Sanctum::actingAs($this->ownerUser, ['*']);

        $response = $this->deleteJson("/api/tasks/{$this->ownerTask->id}");

        $response->assertStatus(204); // No Content

        $this->assertDatabaseMissing('tasks', ['id' => $this->ownerTask->id]);
    }

    /** @test */
    public function guest_cannot_delete_their_own_task()
    {
        Sanctum::actingAs($this->guestUser, ['*']);

        $response = $this->deleteJson("/api/tasks/{$this->guestTask->id}");

        $response->assertStatus(403) // Forbidden
                 ->assertJson(['message' => 'Only owners can delete tasks.']);

        $this->assertDatabaseHas('tasks', ['id' => $this->guestTask->id]); // Task should still exist
    }

    /** @test */
    public function owner_cannot_delete_a_task_they_dont_own()
    {
        Sanctum::actingAs($this->ownerUser, ['*']);

        $response = $this->deleteJson("/api/tasks/{$this->guestTask->id}"); // Trying to delete guest's task

        $response->assertStatus(403) // Forbidden
                 ->assertJson(['message' => 'Unauthorized to delete this task.']);

        $this->assertDatabaseHas('tasks', ['id' => $this->guestTask->id]);
    }
}