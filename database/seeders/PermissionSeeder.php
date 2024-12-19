<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            // Role & Permission Management
            'manage roles',
            'manage permissions',

            // User Management
            'view users',
            'show user',
            'create users',
            'edit users',
            'soft delete user',
            'show deleted users',
            'restore user',
            'force delete user',

            // Category Management
            'view categories',
            'show category',
            'create categories',
            'edit categories',
            'soft delete category',
            'show deleted categories',
            'restore category',
            'force delete category',

            // Product Management
            'view products',
            'show product',
            'create products',
            'edit products',
            'soft delete product',
            'show deleted products',
            'restore product',
            'force delete product',

            // Order Management
            'view orders',
            'show order',
            'create orders',
            'edit orders',
            'soft delete order',
            'show deleted orders',
            'restore order',
            'force delete order',

            // Cart Management
            'view carts',
            'show cart',
            'create carts',
            'edit carts',
            'delete cart',
            'checkout',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission, 'guard_name' => 'api']);
        }
    }
}
