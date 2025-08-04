<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@gmail.com',
            'password' => Hash::make('123456'),
            'is_type' => 1,
        ]);

        User::create([
            'name' => 'Manager User',
            'email' => 'manager@gmail.com',
            'password' => Hash::make('123456'),
            'is_type' => 2,
        ]);

        User::create([
            'name' => 'Normal User',
            'email' => 'user@gmail.com',
            'password' => Hash::make('123456'),
            'is_type' => 0,
        ]);
    }
}
