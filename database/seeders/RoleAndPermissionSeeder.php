<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RoleAndPermissionSeeder extends Seeder
{
    public function run(): void
    {
        $registrar = app(PermissionRegistrar::class);
        $registrar->forgetCachedPermissions();

        $permissions = [
            'students.view',
            'students.manage',
            'courses.view',
            'courses.manage',
            'registrations.view',
            'registrations.manage',
            'users.view',
            'users.manage',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate([
                'name' => $permission,
                'guard_name' => 'web',
            ]);
        }

        $rolePermissions = [
            'administrator' => $permissions,
            'lecturer' => [
                'students.view',
                'courses.view',
                'registrations.view',
            ],
            'student' => [
                'courses.view',
                'registrations.view',
            ],
        ];

        foreach ($rolePermissions as $roleName => $assignedPermissions) {
            $role = Role::firstOrCreate([
                'name' => $roleName,
                'guard_name' => 'web',
            ]);

            $role->syncPermissions($assignedPermissions);
        }

        $registrar->forgetCachedPermissions();
    }
}