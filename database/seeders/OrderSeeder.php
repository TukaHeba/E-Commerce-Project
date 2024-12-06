<?php

namespace Database\Seeders;

use App\Models\Order\Order;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class OrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Order::create([
           'user_id' => 1,
           'shipping_address' => 'homs',
           'status' => 'pending',
           'total_price' => 50.2,
        ]);
        Order::create([
            'user_id' => 1,
            'shipping_address' => 'damas',
            'status' => 'pending',
            'total_price' => 500.2,
         ]);
    }
}
