<?php

namespace Database\Seeders;

use App\Models\Cart\Cart;
use App\Models\CartItem\CartItem;
use App\Models\Order\Order;
use App\Models\Product\Product;
use Illuminate\Database\Seeder;
use App\Models\OrderItem\OrderItem;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class CartItemSeeder extends Seeder
{
    public function run(): void
    {
        for ($x = 0; $x <= 10; $x++)
      {
            $product = Product::inRandomOrder()->first();
                $cart = Cart::inRandomOrder()->first();
                if ($cart) {
                   CartItem::create([
                        'cart_id' => $cart->id,
                        'product_id' => $product->id,
                        'quantity' => rand(1, 5),
                        'created_at'=> fake()->dateTimeBetween(now()->subMonths(2), now()),
                        'updated_at'=>fake()->dateTimeBetween(now()->subMonths(2), now()),
                    ]);


            }
        }
    }
    }
