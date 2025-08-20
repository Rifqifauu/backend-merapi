<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'test@example.com'], // cek kalau sudah ada user dengan email ini
            [
                'name' => 'Test User',
                'password' => Hash::make('password'), // password = "password"
            ]
        );
    }
}
