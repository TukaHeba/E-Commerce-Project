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
            CategorySeeder::class,
            ProductSeeder::class,
            FavoriteSeeder::class,
            CartSeeder::class,
            OrderSeeder::class,
            OrderTrackingSeeder::class,
            OrderItemSeeder::class,
            PhotoSeeder::class,
            RateSeeder::class,
            AccountSeeder::class,
            TransactionSeeder::class,
        ]);
    }
}
