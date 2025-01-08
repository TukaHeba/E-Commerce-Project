<?php

namespace Database\Seeders;

use App\Models\User\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;



class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::create([
            'first_name' => 'Admin',
            'last_name' => 'User',
            'email' => 'admin@example.com',
            'email_verified_at' => now(),
            'password' => Hash::make('Adminnn@12345678'),
            'phone' => fake()->phoneNumber,
            'address' => fake()->address,
            'is_male' => fake()->boolean,
            'birthdate' => fake()->date(),
            'telegram_user_id' => fake()->optional()->randomNumber(),
        ]);
        $admin->assignRole('admin');

        $salesmanager = User::create([
            'first_name' => 'SalesManager',
            'last_name' => 'User',
            'email' => 'SalesManager@example.com',
            'email_verified_at' => now(),
            'password' => Hash::make('SalesManager@12345678'),
            'phone' => fake()->phoneNumber,
            'address' => fake()->address,
            'is_male' => fake()->boolean,
            'birthdate' => fake()->date(),
            'telegram_user_id' => fake()->optional()->randomNumber(),
        ]);
        $salesmanager->assignRole('sales manager');

        $storemanager = User::create([
            'first_name' => 'StoreManager',
            'last_name' => 'User',
            'email' => 'StoreManager@example.com',
            'email_verified_at' => now(),
            'password' => Hash::make('StoreManager@12345678'),
            'phone' => fake()->phoneNumber,
            'address' => fake()->address,
            'is_male' => fake()->boolean,
            'birthdate' => fake()->date(),
            'telegram_user_id' => fake()->optional()->randomNumber(),
        ]);
        $storemanager->assignRole('store manager');

        $customer = User::create([
            'first_name' => 'customer1',
            'last_name' => 'User',
            'email' => 'customer1@example.com',
            'email_verified_at' => now(),
            'password' => Hash::make('customer1@12345678'),
            'phone' => fake()->phoneNumber,
            'address' => fake()->address,
            'is_male' => fake()->boolean,
            'birthdate' => fake()->date(),
            'telegram_user_id' => fake()->optional()->randomNumber(),
        ]);
        $customer->assignRole('customer');
        $customer->cart()->create();

        $customer = User::create([
            'first_name' => 'customer2',
            'last_name' => 'User',
            'email' => 'customer2@example.com',
            'email_verified_at' => now(),
            'password' => Hash::make('customer2@12345678'),
            'phone' => fake()->phoneNumber,
            'address' => fake()->address,
            'is_male' => fake()->boolean,
            'birthdate' => fake()->date(),
            'telegram_user_id' => fake()->optional()->randomNumber(),
        ]);
        $customer->assignRole('customer');
        $customer->cart()->create();


        $customer = User::create([
            'first_name' => 'customer3',
            'last_name' => 'User',
            'email' => 'customer3@example.com',
            'email_verified_at' => now(),
            'password' => Hash::make('customer3@12345678'),
            'phone' => fake()->phoneNumber,
            'address' => fake()->address,
            'is_male' => fake()->boolean,
            'birthdate' => fake()->date(),
            'telegram_user_id' => fake()->optional()->randomNumber(),
        ]);
        $customer->assignRole('customer');
        $customer->cart()->create();


        $customer = User::create([
            'first_name' => 'customer4',
            'last_name' => 'User',
            'email' => 'customer4@example.com',
            'email_verified_at' => now(),
            'password' => Hash::make('customer4@12345678'),
            'phone' => fake()->phoneNumber,
            'address' => fake()->address,
            'is_male' => fake()->boolean,
            'birthdate' => fake()->date(),
            'telegram_user_id' => fake()->optional()->randomNumber(),
        ]);
        $customer->assignRole('customer');
        $customer->cart()->create();


        $customer = User::create([
            'first_name' => 'customer5',
            'last_name' => 'User',
            'email' => 'customer5@example.com',
            'email_verified_at' => now(),
            'password' => Hash::make('customer5@12345678'),
            'phone' => fake()->phoneNumber,
            'address' => fake()->address,
            'is_male' => fake()->boolean,
            'birthdate' => fake()->date(),
            'telegram_user_id' => fake()->optional()->randomNumber(),
        ]);
        $customer->assignRole('customer');
        $customer->cart()->create();



        // Create 50 customer users using the factory and assign the customer role
        User::factory()->count(50)->create()->each(function ($user) {
            $user->assignRole('customer');
            $user->cart()->create();
        });
    }
}
