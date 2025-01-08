<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Order\Order;
use App\Models\OrderTracking\OrderTracking;

class OrderTrackingSeeder extends Seeder
{
    public function run(): void
    {

        // Get all orders
        $orders = Order::all();

        foreach ($orders as $order) {
            if ($order->status === "pending") {
                OrderTracking::create([
                    'order_id' => $order->id,
                    'old_status' => null,
                    'new_status' => "pending",
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
            if ($order->status === "shipped") {
                OrderTracking::create([
                    'order_id' => $order->id,
                    'old_status' => "pending",
                    'new_status' => "shipped",
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                OrderTracking::create([
                    'order_id' => $order->id,
                    'old_status' => null,
                    'new_status' => "pending",
                    'created_at' => now()->subDays(3),
                    'updated_at' => now()->subDays(3),
                ]);
            }
            if ($order->status === "delivered") {
                OrderTracking::create([
                    'order_id' => $order->id,
                    'old_status' => null,
                    'new_status' => "pending",
                    'created_at' => now()->subDays(10),
                    'updated_at' => now()->subDays(10),
                ]);
                OrderTracking::create([
                    'order_id' => $order->id,
                    'old_status' => "pending",
                    'new_status' => "shipped",
                    'created_at' => now()->subDays(5),
                    'updated_at' => now()->subDays(5),
                ]);
                OrderTracking::create([
                    'order_id' => $order->id,
                    'old_status' => "shipped",
                    'new_status' => "delivered",
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

            }
            if ($order->status === "canceled") {
                $statuses = ["pending", "shipped"];
                $oldStatus = $statuses[array_rand($statuses)];
                OrderTracking::create([
                    'order_id' => $order->id,
                    'old_status' => $oldStatus,
                    'new_status' => "canceled",
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}
