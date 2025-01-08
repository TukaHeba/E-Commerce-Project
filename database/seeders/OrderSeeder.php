<?php

namespace Database\Seeders;

use App\Models\Cart\Cart;
use App\Models\CartItem\CartItem;
use App\Models\Order\Order;
use App\Models\Product\Product;
use Illuminate\Database\Seeder;
use App\Models\OrderItem\OrderItem;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class OrderSeeder extends Seeder
{
    public function run(): void
    {
        // Create orders with associated products
        Order::withoutEvents(function () {
            Order::factory(30)->create()->each(function ($order) {
            $products = Product::inRandomOrder()->take(rand(1, 5))->get();
            $totalPrice = 0;

            foreach ($products as $product) {
                $cart = Cart::inRandomOrder()->first();

                if ($cart) {
                    $cartItem = CartItem::create([
                        'cart_id' => $cart->id,
                        'product_id' => $product->id,
                        'quantity' => rand(1, 5),
                    ]);

                    $orderItem = OrderItem::create([
                        'order_id' => $order->id,
                        'product_id' => $cartItem->product_id,
                        'quantity' => $cartItem->quantity,
                        'price' => $product->price,
                    ]);

                    $totalPrice += $orderItem->price * $orderItem->quantity;
                }
            }

            $order->total_price = $totalPrice;
            $order->save();
        }); });
    }
}
