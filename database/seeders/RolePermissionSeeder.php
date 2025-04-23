<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\Permission;
use Illuminate\Database\Seeder;

class RolePermissionSeeder extends Seeder
{
    public function run()
    {
        $admin = Role::firstOrCreate(['name' => 'admin']);
        $user = Role::firstOrCreate(['name' => 'user']);

        $permissions = [
            'view movies',
            'create movies',
            'edit movies',
            'delete movies',
            'manage users'
        ];

        foreach ($permissions as $perm) {
            $permission = Permission::firstOrCreate(['name' => $perm]);
            $admin->permissions()->syncWithoutDetaching([$permission->id]);

            if ($perm === 'view movies') {
                $user->permissions()->syncWithoutDetaching([$permission->id]);
            }
        }
    }
}
