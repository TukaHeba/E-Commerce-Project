<?php

namespace Database\Seeders;

use App\Models\Cart\Cart;
use App\Models\Product\Product;
use Illuminate\Database\Seeder;
use App\Models\CartItem\CartItem;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class CartItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        for ($x = 0; $x <= 10; $x++) {
            $product = Product::inRandomOrder()->first();
            $cart = Cart::inRandomOrder()->first();
            if ($cart) {
                $date = fake()->dateTimeBetween('-3 months', '-2 months');
                CartItem::create([
                    'cart_id' => $cart->id,
                    'product_id' => $product->id,
                    'quantity' => rand(1, 5),
                    'created_at' => $date,
                    'updated_at' => $date,
                ]);
            }
        }
    }
}
