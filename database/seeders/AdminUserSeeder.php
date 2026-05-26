<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $branch = Branch::firstOrCreate(
            ['code' => 'HQ'],
            ['name' => 'ສຳນັກງານໃຫຍ່', 'address' => 'ນະຄອນຫຼວງວຽງຈັນ', 'is_active' => true]
        );

        $admin = User::firstOrCreate(
            ['username' => 'admin'],
            [
                'name' => 'Super Admin',
                'email' => 'admin@stockk.com',
                'username' => 'admin',
                'password' => Hash::make('admin123'),
                'role' => 'super_admin',
                'branch_id' => $branch->id,
                'is_active' => true,
            ]
        );
        $admin->assignRole('super_admin');

        $manager = User::firstOrCreate(
            ['username' => 'manager'],
            [
                'name' => 'ຜູ້ຈັດການ',
                'email' => 'manager@stockk.com',
                'username' => 'manager',
                'password' => Hash::make('manager123'),
                'role' => 'manager',
                'branch_id' => $branch->id,
                'is_active' => true,
            ]
        );
        $manager->assignRole('manager');

        $staff = User::firstOrCreate(
            ['username' => 'staff'],
            [
                'name' => 'ພະນັກງານສາງ',
                'email' => 'staff@stockk.com',
                'username' => 'staff',
                'password' => Hash::make('staff123'),
                'role' => 'warehouse_staff',
                'branch_id' => $branch->id,
                'is_active' => true,
            ]
        );
        $staff->assignRole('warehouse_staff');
    }
}
