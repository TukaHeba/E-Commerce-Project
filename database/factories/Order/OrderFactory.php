<?php

namespace Database\Factories\Order;

use App\Models\Address\Address;
use App\Models\Address\Zone;
use App\Models\User\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Order>
 */
class OrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
//            'zone_id' => Zone::factory(),
            'zone_id' => rand(1,420),
            'postal_code' => $this->faker->postcode(),
            'status' => $this->faker->randomElement(['pending', 'shipped', 'delivered', 'canceled']),
            'total_price' => $this->faker->randomFloat(2, 10, 500),
            'created_at' => $this->faker->dateTimeBetween('-1 year', 'now'), // تاريخ عشوائي في آخر سنة
        ];
    }
}
