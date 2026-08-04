<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionSeeder extends Seeder
{
    private const PERMISSIONS = [
        'dashboard.view',
        'orders.view',
        'orders.retry',            // CS: retry / resend-callback / cek status manual
        'orders.force-success',    // Finance: padanan terdekat "approve refund"
        'reports.view',
        'games.manage',            // Games, Categories, Products & SKUs
        'vouchers.manage',
        'flash-sales.manage',
        'cms.manage',              // Banner, FAQ, Pages (T&C/Privasi)
        'providers.manage',
        'api-logs.view',
        'payment-gateways.manage',
        'users.manage',
        'audit-logs.view',
    ];

    private const ROLE_PERMISSIONS = [
        'owner'     => '*',
        'admin'     => '*',
        'finance'   => ['dashboard.view', 'reports.view', 'orders.view', 'orders.force-success'],
        'cs'        => ['dashboard.view', 'orders.view', 'orders.retry'],
        'marketing' => ['dashboard.view', 'vouchers.manage', 'flash-sales.manage', 'cms.manage'],
        'developer' => ['dashboard.view', 'providers.manage', 'api-logs.view', 'payment-gateways.manage'],
    ];

    public function run(): void
    {
        foreach (self::PERMISSIONS as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        foreach (self::ROLE_PERMISSIONS as $roleName => $permissions) {
            $role = Role::firstOrCreate(['name' => $roleName]);

            $permissions = $permissions === '*' ? self::PERMISSIONS : $permissions;

            $role->syncPermissions($permissions);
        }

        $this->command->info('Permission berhasil di-assign ke role: '.implode(', ', array_keys(self::ROLE_PERMISSIONS)));
    }
}