<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Buat Roles
        $superadmin = Role::firstOrCreate(['name' => 'superadmin']);
        $admin = Role::firstOrCreate(['name' => 'admin_cabang']);
        $kasir = Role::firstOrCreate(['name' => 'kasir']);

        // 2. Buat User Superadmin
        $adminUser = User::firstOrCreate(
            ['email' => 'admin@admin.com'],
            [
                'name' => 'Superadmin',
                'password' => bcrypt('password'),
                'branch_id' => 1
            ]
        );
        $adminUser->assignRole('superadmin');

        // 3. Buat User Kasir dummy untuk cabang 1
        $kasirUser = User::firstOrCreate(
            ['email' => 'kasir@admin.com'],
            [
                'name' => 'Kasir Pusat',
                'password' => bcrypt('password'),
                'branch_id' => 1
            ]
        );
        $kasirUser->assignRole('kasir');
    }
}
