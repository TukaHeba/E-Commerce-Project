<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Admin Role
        $admin = Role::create(['name' => 'admin', 'guard_name' => 'api']);
        $admin->syncPermissions(Permission::all());

        // Sales Manager Role
        $salesManager = Role::create(['name' => 'sales manager', 'guard_name' => 'api']);
        $salesManager->syncPermissions(
            Permission::whereIn('name', [
                'view users',
                'show user',
                'view products',
                'show product',
                'view orders',
                'show order',
                'create orders',
                'edit orders',
                'soft delete order',
                'show deleted orders',
                'restore order',
                'force delete order',
            ])->get()
        );

        // Store Manager Role
        $storeManager = Role::create(['name' => 'store manager', 'guard_name' => 'api']);
        $storeManager->syncPermissions(
            Permission::whereIn('name', [
                'view products',
                'show product',
                'create products',
                'edit products',
                'soft delete product',
                'show deleted products',
                'restore product',
                'force delete product',
                'view categories',
                'show category',
                'create categories',
                'edit categories',
            ])->get()
        );

        // Customer Role
        $customer = Role::create(['name' => 'customer', 'guard_name' => 'api']);
        $customer->syncPermissions(
            Permission::whereIn('name', [
                'show user',
                'edit users',
                'soft delete user',    
                'show cart',
                'create carts',
                'edit carts',
                'checkout',
                'view orders',
                'show order',
                'create orders',
            ])->get()
        );
    }
}
