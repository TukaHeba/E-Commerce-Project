<?php

namespace Database\Seeders;

use Carbon\Carbon;
use App\Models\Cart\Cart;
use App\Models\Order\Order;
use App\Models\Product\Product;
use Illuminate\Database\Seeder;
use App\Models\CartItem\CartItem;
use App\Models\OrderItem\OrderItem;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class OrderSeeder extends Seeder
{
    public function run(): void
    {
        $lastYear = Carbon::now()->subYear()->year;

        Order::withoutEvents(function () use ($lastYear) {
            Order::factory(50)->create()->each(function ($order) use ($lastYear) {

                $month = rand(1, 12);

                $date = Carbon::create($lastYear, $month, rand(1, 28));
                $order->created_at = $date;
                $order->updated_at = $date;
                $order->save();

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
            });
        });

        // Create orders with associated products
        Order::withoutEvents(function () {
            Order::factory(50)->create()->each(function ($order) {
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
            });
        });
    }
}
