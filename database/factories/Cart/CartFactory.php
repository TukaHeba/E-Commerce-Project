<?php

namespace Database\Factories\Cart;

use App\Models\User\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Cart\Cart>
 */
class CartFactory extends Factory
{
    public function definition()
    {

        return [
            'user_id' => User::inRandomOrder()->first(),
        ];
    }
}
