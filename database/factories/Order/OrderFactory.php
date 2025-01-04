<?php

namespace Database\Factories\Order;

use App\Models\Address\Address;
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
//            'address_id' => Address::factory(),
            'address_id' => rand(1,50),
            'postal_code' => $this->faker->postcode(),
            'status' => $this->faker->randomElement(['pending', 'shipped', 'delivered', 'canceled']),
            'total_price' => $this->faker->randomFloat(2, 10, 500),
        ];
    }
}
