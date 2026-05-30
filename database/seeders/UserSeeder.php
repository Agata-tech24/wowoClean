<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name'     => 'Admin WowoClean',
            'email'    => 'admin@wowoclean.com',
            'password' => Hash::make('admin123'),
            'role'     => 'admin',
        ]);

        User::create([
            'name'     => 'Operator Lapangan',
            'email'    => 'user@wowoclean.com',
            'password' => Hash::make('user123'),
            'role'     => 'user',
        ]);
    }
}