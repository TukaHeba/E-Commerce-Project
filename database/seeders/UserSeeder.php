<?php

namespace Database\Seeders;

use App\Models\User\User;
use Illuminate\Database\Seeder;


class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create 50 users using the UserFactory
        User::factory()->count(50)->create();
    }
}
