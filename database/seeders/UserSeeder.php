<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Admin Account
        User::create([
            'name' => 'Administrator',
            'username' => 'AdminLab',
            'password' => Hash::make('Lab2026'),
            'role' => 'admin'
        ]);
    }
}
