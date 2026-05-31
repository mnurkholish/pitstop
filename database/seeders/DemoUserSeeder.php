<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoUserSeeder extends Seeder
{
    public function run(): void
    {
        User::withTrashed()->updateOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin PitStop',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'email_verified_at' => now(),
                'deleted_at' => null,
            ],
        );

        User::withTrashed()->updateOrCreate(
            ['email' => 'user@example.com'],
            [
                'name' => 'Kholish',
                'password' => Hash::make('password'),
                'role' => 'user',
                'email_verified_at' => now(),
                'deleted_at' => null,
            ],
        );
    }
}
