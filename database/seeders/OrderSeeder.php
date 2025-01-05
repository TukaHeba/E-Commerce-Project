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
//        Order::create([
//           'user_id' => 1,
//           'address_id' => 1,
//           'postal_code'=>'XXX',
//           'status' => 'pending',
//           'total_price' => 50.2,
//        ]);
//        Order::create([
//            'user_id' => 1,
//            'address_id' => 2,
//            'postal_code'=>'XXX',
//            'status' => 'pending',
//            'total_price' => 500.2,
//         ]);
        Order::factory(1000)->create();

    }
}
