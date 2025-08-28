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
            ['email' => 'adminjeep@merapi.com'], // cek kalau sudah ada user dengan email ini
            [
                'name' => 'Admin',
                'password' => Hash::make('adminjeep123'), 
                'is_admin' => '1'
            ]
        );
    }
}
