<?php

namespace Database\Seeders;

use App\Models\User\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;


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
            'email' => 'admin@gmail.com',
            'password' => Hash::make('Adminnn@12345678'),
            'address' => 'admin address',
            'is_male' => true,
            'birthdate' => '1990-01-01',
        ]);
        $admin->assignRole('admin');

        // Create 50 customer users using the factory and assign the customer role
        User::factory()->count(50)->create()->each(function ($user) {
            $user->assignRole('customer');
        });
    }
}
