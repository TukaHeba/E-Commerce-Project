<?php

namespace Database\Factories\Address;

use App\Models\Address\Country;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\City>
 */
class CityFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->city, // اسم المدينة باستخدام مكتبة Faker
            'country_id' => Country::factory(),
        ];
    }
}
