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
            'name'     => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users,username',
            'password' => 'required|string|min:6',
        ]);

        User::create([
            'name'     => $validated['name'],
            'username' => $validated['username'],
            'password' => Hash::make($validated['password']),
            'role'     => 'admin',
        ]);

        return response()->json(['success' => true, 'message' => 'Admin created']);
    }

    /**
     * Update own profile (username & password) for the logged-in super admin.
     */
    public function updateProfile(Request $request)
    {
        if (!auth()->check() || auth()->user()->role !== 'super_admin') {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $user = auth()->user();

        $validated = $request->validate([
            'username'         => 'sometimes|string|max:255|unique:users,username,' . $user->id,
            'password'         => 'sometimes|string|min:6|confirmed',
            'password_confirmation' => 'sometimes|string',
            'current_password' => 'required|string',
        ]);

        // Verifikasi password lama sebelum mengizinkan perubahan
        if (!Hash::check($validated['current_password'], $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Password saat ini tidak sesuai.',
            ], 422);
        }

        if (!empty($validated['username'])) {
            $user->username = $validated['username'];
        }

        if (!empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();

        return response()->json([
            'success'  => true,
            'message'  => 'Profil berhasil diperbarui.',
            'username' => $user->username,
        ]);
    }
}
