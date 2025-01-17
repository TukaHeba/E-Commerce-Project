<?php

namespace Database\Seeders;

use App\Models\User\User;
use App\Services\Photo\PhotoService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;


class UserSeeder extends Seeder
{
    protected PhotoService $photoService;

    public function __construct(PhotoService $photoService)
    {
        $this->photoService = $photoService;
    }

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::create([
            'first_name' => 'Admin',
            'last_name' => 'User',
            'email' => 'Admin@example.com',
            'email_verified_at' => now(),
            'password' => Hash::make('Adminnn@12345678'),
            'phone' => fake()->phoneNumber,
            'address' => fake()->address,
            'is_male' => fake()->boolean,
            'birthdate' => fake()->date(),
            'telegram_user_id' => null,
        ]);
        $admin->assignRole('admin');
        $this->photoService->addDefaultAvatar($admin);

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
            'telegram_user_id' => null,
        ]);
        $salesmanager->assignRole('sales manager');
        $this->photoService->addDefaultAvatar($salesmanager);


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
            'telegram_user_id' => null,
        ]);
        $storemanager->assignRole('store manager');
        $this->photoService->addDefaultAvatar($storemanager);


        $customer = User::create([
            'first_name' => 'customer1',
            'last_name' => 'User',
            'email' => 'customer1@example.com',
            'email_verified_at' => now(),
            'password' => Hash::make('Customer1@12345678'),
            'phone' => fake()->phoneNumber,
            'address' => fake()->address,
            'is_male' => fake()->boolean,
            'birthdate' => fake()->date(),
            'telegram_user_id' => null,
        ]);
        $customer->assignRole('customer');
        $this->photoService->addDefaultAvatar($customer);
        $customer->cart()->create();

        $customer = User::create([
            'first_name' => 'customer2',
            'last_name' => 'User',
            'email' => 'customer2@example.com',
            'email_verified_at' => now(),
            'password' => Hash::make('Customer2@12345678'),
            'phone' => fake()->phoneNumber,
            'address' => fake()->address,
            'is_male' => fake()->boolean,
            'birthdate' => fake()->date(),
            'telegram_user_id' => null,
        ]);
        $customer->assignRole('customer');
        $this->photoService->addDefaultAvatar($customer);
        $customer->cart()->create();


        $customer = User::create([
            'first_name' => 'customer3',
            'last_name' => 'User',
            'email' => 'customer3@example.com',
            'email_verified_at' => now(),
            'password' => Hash::make('Customer3@12345678'),
            'phone' => fake()->phoneNumber,
            'address' => fake()->address,
            'is_male' => fake()->boolean,
            'birthdate' => fake()->date(),
            'telegram_user_id' => null,
        ]);
        $customer->assignRole('customer');
        $this->photoService->addDefaultAvatar($customer);
        $customer->cart()->create();


        $customer = User::create([
            'first_name' => 'customer4',
            'last_name' => 'User',
            'email' => 'customer4@example.com',
            'email_verified_at' => now(),
            'password' => Hash::make('Customer4@12345678'),
            'phone' => fake()->phoneNumber,
            'address' => fake()->address,
            'is_male' => fake()->boolean,
            'birthdate' => fake()->date(),
            'telegram_user_id' => null,
        ]);
        $customer->assignRole('customer');
        $this->photoService->addDefaultAvatar($customer);
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
            'telegram_user_id' => null,
        ]);
        $customer->assignRole('customer');
        $this->photoService->addDefaultAvatar($customer);
        $customer->cart()->create();

        // Create 50 customer users using the factory and assign the customer role
        User::factory()->count(50)->create()->each(function ($user) {
            $user->assignRole('customer');
            $this->photoService->addDefaultAvatar($user);
            $user->cart()->create();
        });
    }
}
