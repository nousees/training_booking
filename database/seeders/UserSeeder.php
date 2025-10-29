<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'Owner',
            'email' => 'owner@test.com',
            'password' => Hash::make('password'),
            'role' => 'owner',
            'phone' => '+79990000001',
        ]);

        User::create([
            'name' => 'Manager', 
            'email' => 'manager@test.com',
            'password' => Hash::make('password'),
            'role' => 'manager',
            'phone' => '+79990000002',
        ]);

        User::create([
            'name' => 'User',
            'email' => 'user@test.com', 
            'password' => Hash::make('password'),
            'role' => 'user',
            'phone' => '+79990000003',
        ]);
    }
}