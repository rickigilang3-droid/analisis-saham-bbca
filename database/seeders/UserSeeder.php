<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Admin - Ricki
        User::create([
            'name'     => 'Ricki',
            'email'    => 'ricki@gmail.com',
            'password' => Hash::make('rigskind'),
            'role'     => 'admin',
            'is_active' => true, // ← tambah ini
            'stock'    => 'BBCA',
        ]);

        // User - Amelia
        User::create([
            'name'     => 'Amelia',
            'email'    => 'amelia@gmail.com',
            'password' => Hash::make('rigskind'),
            'role'     => 'user',
            'is_active' => true, // ← tambah ini
            'stock'    => 'BBCA',
        ]);
    }
}