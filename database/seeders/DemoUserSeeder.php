<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoUserSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin PitStop',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'email_verified_at' => now(),
            ],
        );

        User::updateOrCreate(
            ['email' => 'user@example.com'],
            [
                'name' => 'Santosi',
                'password' => Hash::make('password'),
                'role' => 'user',
                'email_verified_at' => now(),
            ],
        );
    }
}
