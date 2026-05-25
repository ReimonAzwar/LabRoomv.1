<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\JsonResponse;

class AdminAccountController extends Controller
{
    /**
     * List all admin accounts.
     */
    public function index(): JsonResponse
    {
        $admins = User::where('role', 'admin')->get(['id', 'name', 'username']);
        return response()->json(['admins' => $admins]);
    }

    /**
     * Store a new admin account. Only accessible by super admin (assumed middleware).
     */
    public function store(Request $request): JsonResponse
    {
        if (auth()->check() && auth()->user()->role !== 'super_admin') {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users,username',
            'password' => 'required|string|min:6',
        ]);

        User::create([
            'name' => $validated['name'],
            'username' => $validated['username'],
            'password' => Hash::make($validated['password']),
            'role' => 'admin',
        ]);

        return response()->json(['success' => true, 'message' => 'Admin created']);
    }

    /**
     * Update an existing admin account.
     */
    public function update(Request $request, $id): JsonResponse
    {
        if (auth()->check() && auth()->user()->role !== 'super_admin') {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $admin = User::findOrFail($id);
        if ($admin->role !== 'admin') {
            return response()->json(['success' => false, 'message' => 'Not an admin account'], 400);
        }

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'username' => 'sometimes|string|max:255|unique:users,username,' . $admin->id,
            'password' => 'sometimes|string|min:6',
        ]);

        if (isset($validated['name'])) $admin->name = $validated['name'];
        if (isset($validated['username'])) $admin->username = $validated['username'];
        if (isset($validated['password'])) $admin->password = Hash::make($validated['password']);
        $admin->save();

        return response()->json(['success' => true, 'message' => 'Admin updated']);
    }

    /**
     * Delete an admin account.
     */
    public function destroy($id): JsonResponse
    {
        if (auth()->check() && auth()->user()->role !== 'super_admin') {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $admin = User::findOrFail($id);
        if ($admin->role !== 'admin') {
            return response()->json(['success' => false, 'message' => 'Not an admin account'], 400);
        }
        $admin->delete();
        return response()->json(['success' => true, 'message' => 'Admin deleted']);
    }
}
