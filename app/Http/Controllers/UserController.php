<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    private function authorizeOwner()
    {
        if (Auth::user()->role !== 'owner') {
            return response()->json(['message' => 'Unauthorized. Only owners can perform this action.'], 403);
        }
        return null;
    }

    public function index(Request $request)
    {
        if ($response = $this->authorizeOwner()) {
            return $response;
        }

        $perPage = $request->input('per_page', 10);
        return User::paginate($perPage);
    }

    public function store(Request $request)
    {
        if ($response = $this->authorizeOwner()) {
            return $response;
        }

        $validator = Validator::make($request->all(), [
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'role'     => 'required|string|in:owner,guest,user',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $validated = $validator->validated();

        $user = User::create([
            'name'     => $validated['name'],
            'email'    => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role'     => $validated['role'],
        ]);

        return response()->json([
            'message' => 'User created successfully',
            'user'    => $user
        ], 201);
    }

    public function show(User $user)
    {
        if ($response = $this->authorizeOwner()) {
            return $response;
        }

        return $user;
    }

    public function update(Request $request, User $user)
    {
        if ($response = $this->authorizeOwner()) {
            return $response;
        }

        $validator = Validator::make($request->all(), [
            'name'     => 'sometimes|string|max:255',
            'email'    => 'sometimes|email|unique:users,email,' . $user->id,
            'password' => 'sometimes|string|min:6',
            'role'     => 'sometimes|string|in:owner,guest,user',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $validated = $validator->validated();

        if (isset($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        }

        $user->update($validated);

        return response()->json([
            'message' => 'User updated successfully',
            'user'    => $user
        ]);
    }

    public function destroy(User $user)
    {
        if ($response = $this->authorizeOwner()) {
            return $response;
        }

        if (Auth::id() === $user->id) {
            return response()->json(['message' => 'You cannot delete your own user account.'], 403);
        }

        $user->delete();

        return response()->json(['message' => 'User deleted successfully'], 204);
    }

    public function me()
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        return $user;
    }
}
