<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name' => 'Super Administrator',
            'username' => 'superadmin',
            'password' => Hash::make('super@2026'),
            'role' => 'super_admin',
        ]);
    }
}
