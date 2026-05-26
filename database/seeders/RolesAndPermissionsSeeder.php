<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = [
            'manage_users',
            'manage_products',
            'manage_warehouses',
            'manage_branches',
            'manage_suppliers',
            'approve_requests',
            'issue_stock',
            'view_reports',
            'export_reports',
            'manage_transfers',
            'view_audit_logs',
            'manage_backups',
        ];

        foreach ($permissions as $perm) {
            Permission::firstOrCreate(['name' => $perm]);
        }

        $superAdmin = Role::firstOrCreate(['name' => 'super_admin']);
        $superAdmin->givePermissionTo(Permission::all());

        $admin = Role::firstOrCreate(['name' => 'admin']);
        $admin->givePermissionTo([
            'manage_users', 'manage_products', 'manage_warehouses',
            'manage_branches', 'manage_suppliers', 'approve_requests',
            'issue_stock', 'view_reports', 'export_reports', 'manage_transfers',
        ]);

        $manager = Role::firstOrCreate(['name' => 'manager']);
        $manager->givePermissionTo([
            'manage_products', 'approve_requests', 'view_reports',
            'export_reports', 'manage_transfers',
        ]);

        $warehouseStaff = Role::firstOrCreate(['name' => 'warehouse_staff']);
        $warehouseStaff->givePermissionTo([
            'issue_stock', 'manage_transfers', 'view_reports',
        ]);

        Role::firstOrCreate(['name' => 'staff']);
    }
}
