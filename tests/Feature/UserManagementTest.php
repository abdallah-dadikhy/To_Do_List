<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\Sanctum;

class UserManagementTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected User $ownerUser;
    protected User $guestUser;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(); // Seed default users (owner@example.com, guest@example.com)
        $this->ownerUser = User::where('email', 'owner@example.com')->first();
        $this->guestUser = User::where('email', 'guest@example.com')->first();
    }

    /** @test */
    public function owner_can_view_all_users()
    {
        Sanctum::actingAs($this->ownerUser, ['*']);
        User::factory(3)->create(); // Create additional users

        $response = $this->getJson('/api/users');

        $response->assertOk()
                 ->assertJsonCount(5, 'data'); // 2 seeded + 3 new
    }

    /** @test */
    public function guest_cannot_view_all_users()
    {
        Sanctum::actingAs($this->guestUser, ['*']);

        $response = $this->getJson('/api/users');

        $response->assertStatus(403)
                 ->assertJson(['message' => 'Unauthorized. Only owners can perform this action.']);
    }

    /** @test */
    public function owner_can_create_a_new_user()
    {
        Sanctum::actingAs($this->ownerUser, ['*']);

        $newUserData = [
            'name' => 'New Invited User',
            'email' => 'new.user@example.com',
            'password' => 'newpassword123',
            'role' => 'guest',
        ];

        $response = $this->postJson('/api/users', $newUserData);

        $response->assertStatus(201)
                 ->assertJson([
                     'message' => 'User created successfully',
                     'user' => ['email' => 'new.user@example.com', 'role' => 'guest']
                 ]);

        $this->assertDatabaseHas('users', ['email' => 'new.user@example.com']);
    }

    /** @test */
    public function guest_cannot_create_a_new_user()
    {
        Sanctum::actingAs($this->guestUser, ['*']);

        $newUserData = [
            'name' => 'Unauthorized User',
            'email' => 'unauth@example.com',
            'password' => 'password',
            'role' => 'guest',
        ];

        $response = $this->postJson('/api/users', $newUserData);

        $response->assertStatus(403);
        $this->assertDatabaseMissing('users', ['email' => 'unauth@example.com']);
    }

    /** @test */
    public function owner_can_update_another_user()
    {
        Sanctum::actingAs($this->ownerUser, ['*']);

        $userToUpdate = User::factory()->create(['role' => 'guest']);
        $updatedData = [
            'name' => 'Updated Guest Name',
            'role' => 'owner', // Change role
        ];

        $response = $this->putJson("/api/users/{$userToUpdate->id}", $updatedData);

        $response->assertOk()
                 ->assertJson([
                     'message' => 'User updated successfully',
                     'user' => ['name' => 'Updated Guest Name', 'role' => 'owner']
                 ]);

        $this->assertDatabaseHas('users', [
            'id' => $userToUpdate->id,
            'name' => 'Updated Guest Name',
            'role' => 'owner',
        ]);
    }

    /** @test */
    public function owner_cannot_change_their_own_role()
    {
        Sanctum::actingAs($this->ownerUser, ['*']);

        $updatedData = [
            'role' => 'guest', // Trying to change own role to guest
        ];

        $response = $this->putJson("/api/users/{$this->ownerUser->id}", $updatedData);

        $response->assertStatus(403)
                 ->assertJson(['message' => 'You cannot change your own role.']);

        $this->assertDatabaseHas('users', [
            'id' => $this->ownerUser->id,
            'role' => 'owner', // Role should remain owner
        ]);
    }

    /** @test */
    public function owner_can_delete_another_user()
    {
        Sanctum::actingAs($this->ownerUser, ['*']);

        $userToDelete = User::factory()->create(['role' => 'guest']);

        $response = $this->deleteJson("/api/users/{$userToDelete->id}");

        $response->assertStatus(204); // No Content
        $this->assertDatabaseMissing('users', ['id' => $userToDelete->id]);
    }

    /** @test */
    public function owner_cannot_delete_their_own_account()
    {
        Sanctum::actingAs($this->ownerUser, ['*']);

        $response = $this->deleteJson("/api/users/{$this->ownerUser->id}");

        $response->assertStatus(403)
                 ->assertJson(['message' => 'You cannot delete your own user account.']);

        $this->assertDatabaseHas('users', ['id' => $this->ownerUser->id]);
    }
}