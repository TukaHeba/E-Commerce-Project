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
                'OrdersLateToDeliver',
                'BestSellingProducts',
                'BestSellingCategories',
                'CountriesWithHighestOrders',
            ])->get()
        );

        // Store Manager Role
        $storeManager = Role::create(['name' => 'store manager', 'guard_name' => 'api']);
        $storeManager->syncPermissions(
            Permission::whereIn('name', [
                'ProductsRemainingInCarts',
                'ProductsLowOnStock',
                'BestSellingProducts',
                'BestSellingCategories',
                'ProductsNeverBeenSold',
                'CountriesWithHighestOrders',
            ])->get()
        );

        // Customer Role
        $admin = Role::create(['name' => 'customer', 'guard_name' => 'api']);
    }
}
