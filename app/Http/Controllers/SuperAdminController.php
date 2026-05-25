<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class SuperAdminController extends Controller
{
    /**
     * Store a new admin user.
     * Only accessible by a super admin.
     */
    public function store(Request $request)
    {
        // Simple role check (assumes auth is set up)
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
}
