<?php

namespace Database\Seeders;

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
        CartItem::create([
            'cart_id' => 1,
            'product_id' => 1,
            'quantity' => 2,
        ]);
        CartItem::create([
            'cart_id' => 1,
            'product_id' => 2,
            'quantity' => 2,
        ]);
        CartItem::create([
            'cart_id' => 1,
            'product_id' => 3,
            'quantity' => 1,
        ]);
        CartItem::create([
            'cart_id' => 1,
            'product_id' => 4,
            'quantity' => 4,
        ]);
        CartItem::create([
            'cart_id' => 1,
            'product_id' => 5,
            'quantity' => 1,
        ]);
    }
}
