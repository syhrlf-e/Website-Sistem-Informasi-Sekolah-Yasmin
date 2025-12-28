<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Seeder;
use App\Models\User;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Super Admin (hanya boleh 1)
        User::updateOrCreate(
            ['email' => 'sma.yasmin@gmail.com'],
            [
                'name' => 'Super Yasmin',
                'role' => 'super_admin', 
                'password' => Hash::make('yasmin12345')
            ]
        );

        // Admin Biasa 
        User::updateOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin Content',
                'role' => 'admin', 
                'password' => Hash::make('admin12345')
            ]
        );
    }
}