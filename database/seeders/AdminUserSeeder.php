<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::firstOrCreate(
            ['email' => 'admin@topupgame.test'],
            [
                'name' => 'Admin Owner',
                'password' => bcrypt('password'),
                'role' => 'admin',
            ]
        );

        $user->assignRole('owner');

        $this->command->info('Admin user dibuat: admin@topupgame.test / password (GANTI sebelum production!)');
    }
}