<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\RolePermission;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create permissions
        $permissions = [
            [
                'name' => 'View All Users',
                'slug' => 'view-all-users',
                'description' => 'Can view all users in the system'
            ],
            [
                'name' => 'Create Users',
                'slug' => 'create-users',
                'description' => 'Can create new users'
            ],
            [
                'name' => 'Update Users',
                'slug' => 'update-users',
                'description' => 'Can update user information'
            ],
            [
                'name' => 'Delete Users',
                'slug' => 'delete-users',
                'description' => 'Can delete users from the system'
            ],
            [
                'name' => 'View Own Data',
                'slug' => 'view-own-data',
                'description' => 'Can view only their own data'
            ],
            [
                'name' => 'Manage Permissions',
                'slug' => 'manage-permissions',
                'description' => 'Can manage user permissions'
            ]
        ];

        foreach ($permissions as $permission) {
            Permission::create($permission);
        }

        // Assign permissions to roles
        $rolePermissions = [
            // SuperAdmin - All permissions
            ['role' => 'SuperAdmin', 'permission_id' => 1],
            ['role' => 'SuperAdmin', 'permission_id' => 2],
            ['role' => 'SuperAdmin', 'permission_id' => 3],
            ['role' => 'SuperAdmin', 'permission_id' => 4],
            ['role' => 'SuperAdmin', 'permission_id' => 5],
            ['role' => 'SuperAdmin', 'permission_id' => 6],

            // Admin - Limited permissions (no delete, no manage permissions)
            ['role' => 'Admin', 'permission_id' => 1],
            ['role' => 'Admin', 'permission_id' => 2],
            ['role' => 'Admin', 'permission_id' => 3],
            ['role' => 'Admin', 'permission_id' => 5],

            // User - Only own data
            ['role' => 'User', 'permission_id' => 5],
        ];

        foreach ($rolePermissions as $rolePermission) {
            RolePermission::create($rolePermission);
        }
    }
}
