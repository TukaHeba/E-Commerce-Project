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
            UserSeeder::class,
            MainCategorySeeder::class,
            SubCategorySeeder::class,
            ProductSeeder::class,
            FavoriteSeeder::class,
            CartItemSeeder::class,
            AddressSeeder::class,
            OrderSeeder::class,
            OrderTrackingSeeder::class,
            OrderItemSeeder::class,
            PhotoSeeder::class,
            RateSeeder::class,
        ]);
    }
}
