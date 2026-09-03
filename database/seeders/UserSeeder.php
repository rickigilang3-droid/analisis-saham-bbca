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
            'name'      => 'Ricki',
            'email'     => 'ricki@gmail.com',
            'password'  => Hash::make('rigskind'),
            'role'      => 'admin',
            'status'    => 'active',
            'is_active' => true,
            'balance'   => 100000000,
            'lots'      => 0,
            'avg_price' => 0,
            'stock'     => 'BBCA',
        ]);

        // User - Amelia
        User::create([
            'name'      => 'Amelia',
            'email'     => 'amelia@gmail.com',
            'password'  => Hash::make('rigskind'),
            'role'      => 'user',
            'status'    => 'active',
            'is_active' => true,
            'balance'   => 100000000,
            'lots'      => 0,
            'avg_price' => 0,
            'stock'     => 'BBCA',
        ]);
    }
}