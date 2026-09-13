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

        // 2. Beri role superadmin ke User dengan ID 1 (User default test)
        $user = User::find(1);
        if ($user) {
            $user->assignRole('superadmin');
        } else {
            $user = User::create([
                'name' => 'Superadmin',
                'email' => 'admin@admin.com',
                'password' => bcrypt('password'),
                'branch_id' => 1
            ]);
            $user->assignRole('superadmin');
        }

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
