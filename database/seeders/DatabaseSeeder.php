<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@pradanmotor.test'],
            [
                'name' => 'Admin Pradan Motor',
                'password' => Hash::make('admin12345'),
                'role' => 'admin',
            ],
        );

        User::updateOrCreate(
            ['email' => 'kasir@pradanmotor.test'],
            [
                'name' => 'Kasir Pradan Motor',
                'password' => Hash::make('kasir12345'),
                'role' => 'kasir',
            ],
        );
    }
}
