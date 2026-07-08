<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = ['owner', 'admin', 'finance', 'cs', 'marketing', 'developer'];

        foreach ($roles as $role) {
            Role::firstOrCreate(['name' => $role]);
        }

        $this->command->info('Role berhasil dibuat: ' . implode(', ', $roles));
    }
}