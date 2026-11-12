<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = [
            'dashboard.view',
            'users.manage',
            'roles.manage',
            'products.manage',
            'inventory.manage',
            'customers.manage',
            'orders.view',
            'orders.manage',
            'payments.manage',
            'payments.verify',
            'custom_orders.manage',
            'production.manage',
            'production.update',
            'deliveries.manage',
            'deliveries.update',
            'expenses.manage',
            'reports.view',
            'reports.financial',
            'settings.manage',
            'marketing.manage',
        ];

        foreach ($permissions as $permission) {
            Permission::findOrCreate($permission);
        }

        $owner = Role::findOrCreate('owner');
        $owner->syncPermissions($permissions);

        $manager = Role::findOrCreate('manager');
        $manager->syncPermissions([
            'dashboard.view',
            'products.manage',
            'inventory.manage',
            'customers.manage',
            'orders.view',
            'orders.manage',
            'payments.manage',
            'custom_orders.manage',
            'production.manage',
            'deliveries.manage',
            'reports.view',
            'marketing.manage',
        ]);

        Role::findOrCreate('salesperson')->syncPermissions([
            'dashboard.view',
            'customers.manage',
            'orders.view',
            'orders.manage',
            'payments.manage',
        ]);

        Role::findOrCreate('inventory_manager')->syncPermissions([
            'dashboard.view',
            'products.manage',
            'inventory.manage',
            'reports.view',
        ]);

        Role::findOrCreate('production_staff')->syncPermissions([
            'dashboard.view',
            'production.update',
            'custom_orders.manage',
        ]);

        Role::findOrCreate('delivery_staff')->syncPermissions([
            'dashboard.view',
            'deliveries.update',
        ]);

        Role::findOrCreate('customer')->syncPermissions([]);
    }
}
