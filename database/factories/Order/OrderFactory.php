<?php

namespace Database\Factories\Order;

use App\Models\User\User;
use Illuminate\Support\Str;
use App\Models\Address\Zone;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Order\Order>
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

            'user_id' => User::inRandomOrder()->first(),
            'zone_id' => Zone::inRandomOrder()->first(),
            'postal_code' => fake()->postcode,
            'status' => fake()->randomElement(['pending', 'shipped', 'delivered', 'canceled']),
            'total_price' => fake()->randomFloat(2, 10, 1000),
            'order_number' => Str::uuid(),
            'created_at' => fake()->dateTimeBetween('-1 year', 'now'),
        ];
    }
}
