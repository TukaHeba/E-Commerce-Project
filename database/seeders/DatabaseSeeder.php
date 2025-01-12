<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            PermissionSeeder::class,
            RoleSeeder::class,
            AddressSeeder::class,
            UserSeeder::class,
            MainCategorySeeder::class,
            SubCategorySeeder::class,
            MainCategorySubCategorySeeder::class,
            ProductSeeder::class,
            CartItemSeeder::class,            OrderSeeder::class,
            OrderTrackingSeeder::class,
            RateSeeder::class,
            FavoriteSeeder::class,
            // PhotoSeeder::class,
        ]);
    }
}
