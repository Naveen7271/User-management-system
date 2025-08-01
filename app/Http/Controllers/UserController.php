<?php

namespace App\Http\Controllers;

use App\Jobs\BulkCreateUsers;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $cacheKey = "users_list_{$user->role}_{$user->id}";

        return Cache::remember($cacheKey, 300, function () use ($user) {
            if ($user->isSuperAdmin()) {
                // SuperAdmin can see all users (including Admins and Users)
                return User::withTrashed()->get();
            } elseif ($user->isAdmin()) {
                // Admin can see all users (but not SuperAdmins or Admins)
                return User::where('role', 'User')->withTrashed()->get();
            } else {
                // Users can only see their own data
                return User::where('id', $user->id)->get();
            }
        });
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $user = $request->user();

        if (!$user->canManageUsers()) {
            return response()->json(['message' => 'Access denied. Insufficient permissions.'], 403);
        }

        $validator = Validator::make($request->all(), [
            'users' => 'required|array|min:1',
            'users.*.name' => 'required|string|max:255',
            'users.*.email' => 'required|email|unique:users,email',
            'users.*.password' => 'required|string|min:8',
            'users.*.role' => 'required|in:User,Admin,SuperAdmin',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Check if Admin is trying to create SuperAdmin
        if ($user->isAdmin()) {
            foreach ($request->users as $userData) {
                if ($userData['role'] === 'SuperAdmin' || $userData['role'] === 'Admin') {
                    return response()->json(['message' => 'Admins cannot create SuperAdmin or Admin users.'], 403);
                }
            }
        }

        // If multiple users, process in background
        if (count($request->users) > 1) {
            BulkCreateUsers::dispatch($request->users);
            
            return response()->json([
                'message' => 'Users are being created in the background. You will be notified when complete.',
                'users_count' => count($request->users)
            ]);
        }

        // Single user creation
        $userData = $request->users[0];
        $newUser = User::create([
            'name' => $userData['name'],
            'email' => $userData['email'],
            'password' => Hash::make($userData['password']),
            'role' => $userData['role'],
        ]);

        // Clear cache
        Cache::forget("users_list_{$user->role}_{$user->id}");

        return response()->json([
            'message' => 'User created successfully',
            'user' => $newUser
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $userId)
    {
        $user = $request->user();
        $targetUser = User::find($userId);

        if (!$targetUser) {
            return response()->json(['message' => 'User not found'], 404);
        }

        // Check permissions
        if (!$user->canManageUsers() && $user->id != $userId) {
            return response()->json(['message' => 'Access denied. Insufficient permissions.'], 403);
        }

        // Admin cannot update SuperAdmin or other Admins
        if ($user->isAdmin() && ($targetUser->isSuperAdmin() || $targetUser->isAdmin())) {
            return response()->json(['message' => 'Admins cannot update SuperAdmin or Admin users.'], 403);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|unique:users,email,' . $userId,
            'password' => 'sometimes|string|min:8',
            'role' => 'sometimes|in:User,Admin,SuperAdmin',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Admin cannot change role to SuperAdmin or Admin
        if ($user->isAdmin() && isset($request->role) && ($request->role === 'SuperAdmin' || $request->role === 'Admin')) {
            return response()->json(['message' => 'Admins cannot assign SuperAdmin or Admin roles.'], 403);
        }

        $updateData = $request->only(['name', 'email', 'role']);
        
        if ($request->has('password')) {
            $updateData['password'] = Hash::make($request->password);
        }

        $targetUser->update($updateData);

        // Clear cache
        Cache::forget("users_list_{$user->role}_{$user->id}");

        return response()->json([
            'message' => 'User updated successfully',
            'user' => $targetUser
        ]);
    }

    /**
     * Remove the specified resource from storage (Soft Delete).
     */
    public function destroy(Request $request, $userId)
    {
        $user = $request->user();
        $targetUser = User::find($userId);

        if (!$targetUser) {
            return response()->json(['message' => 'User not found'], 404);
        }

        if (!$user->canDeleteUsers()) {
            return response()->json(['message' => 'Access denied. Only SuperAdmin can delete users.'], 403);
        }

        // SuperAdmin cannot delete themselves
        if ($user->id == $userId) {
            return response()->json(['message' => 'Cannot delete your own account.'], 403);
        }

        $targetUser->delete();

        // Clear cache
        Cache::forget("users_list_{$user->role}_{$user->id}");

        return response()->json([
            'message' => 'User deleted successfully'
        ]);
    }
}
