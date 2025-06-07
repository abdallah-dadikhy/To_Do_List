<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\Sanctum;

class AuthTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /** @test */
    public function a_user_can_register()
    {
        $userData = [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ];

        $response = $this->postJson('/api/register', $userData);

        $response->assertStatus(201)
                 ->assertJsonStructure(['message', 'user', 'token'])
                 ->assertJson([
                     'message' => 'User registered successfully',
                     'user' => ['email' => 'test@example.com', 'role' => 'guest']
                 ]);

        $this->assertDatabaseHas('users', ['email' => 'test@example.com', 'role' => 'guest']);
    }

    /** @test */
    public function a_user_can_login_with_correct_credentials()
    {
        $user = User::factory()->create([
            'email' => 'login@example.com',
            'password' => Hash::make('password'),
            'role' => 'guest',
        ]);

        $loginData = [
            'email' => 'login@example.com',
            'password' => 'password',
        ];

        $response = $this->postJson('/api/login', $loginData);

        $response->assertOk() // status 200
                 ->assertJsonStructure(['message', 'user', 'token'])
                 ->assertJson([
                     'message' => 'Logged in successfully',
                     'user' => ['email' => 'login@example.com']
                 ]);
    }

    /** @test */
    public function a_user_cannot_login_with_incorrect_credentials()
    {
        User::factory()->create([
            'email' => 'wrong@example.com',
            'password' => Hash::make('correctpassword'),
        ]);

        $loginData = [
            'email' => 'wrong@example.com',
            'password' => 'incorrectpassword',
        ];

        $response = $this->postJson('/api/login', $loginData);

        $response->assertStatus(422) // Validation error or unauthorized
                 ->assertJsonValidationErrors('email'); // Assert that email field has validation error
    }

    /** @test */
    public function authenticated_user_can_access_their_own_details()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user, ['*']); // Simulate authenticated user

        $response = $this->getJson('/api/me'); // استخدام /api/me كما طلبته

        $response->assertOk()
                 ->assertJson(['id' => $user->id, 'email' => $user->email]);
    }

    /** @test */
    public function unauthenticated_user_cannot_access_protected_route()
    {
        $response = $this->getJson('/api/me');

        $response->assertStatus(401)
                 ->assertJson(['message' => 'Unauthenticated.']);
    }
}