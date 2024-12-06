<?php

namespace Database\Seeders;

use App\Models\OrderItem\OrderItem;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class OrderItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        OrderItem::create([
            'order_id' => 1,
            'product_id' => 2,
            'quantity' => 4,
            'price' => 10.2,
         ]);
         OrderItem::create([
            'order_id' => 1,
            'product_id' => 4,
            'quantity' => 4,
            'price' => 90.2,
         ]);
         OrderItem::create([
            'order_id' => 1,
            'product_id' => 5,
            'quantity' => 4,
            'price' => 70.2,
         ]);
         OrderItem::create([
            'order_id' => 1,
            'product_id' => 3,
            'quantity' => 4,
            'price' => 600.2,
         ]);
         OrderItem::create([
            'order_id' => 2,
            'product_id' => 3,
            'quantity' => 40,
            'price' => 200.25,
         ]);
         OrderItem::create([
            'order_id' => 2,
            'product_id' => 4,
            'quantity' => 45,
            'price' => 40.2,
         ]);
    }
}
