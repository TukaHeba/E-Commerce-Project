<?php

namespace App\Services\Order;

use App\Models\Order\Order;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class OrderService
{
    /**
     * List of orders related to user
     * @param array $data
     * @return mixed
     */
    public function getOrders(array $data)
    {
        $orders = Cache::remember('orders', 1200, function () use ($data) {
            return Order::filter($data)->where('user_id', Auth::id())->paginate(10);
        });
        return $orders;
    }

    /**
     * Create new order
     * @param array $data
     * @return void
     */
    public function storeOrder(array $data)
    {
        //
    }

    /**
     * Update order status
     * @param \App\Models\Order\Order $order
     * @param array $data
     * @return array
     */
    public function updateOrder(Order $order, array $data)
    {
        try {
            if ($order->user_id !== Auth::id()) {
                return [
                    'status' => false,
                    'msg' => 'You do not have permission to access this resource.',
                    'code' => 403
                ];
            }
            $order->update(array_filter($data));
            return [
                'status' => true,
                'order' => $order
            ];

        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return [
                'status' => false,
                'msg' => 'A server error has occurred',
                'code' => 500
            ];
        }
    }

    /**
     * Soft delete order
     * @param \App\Models\Order\Order $order
     * @return array
     */
    public function destroyOrder(Order $order)
    {
        try {
            if ($order && $order->user_id !== Auth::id()) {
                return [
                    'status' => false,
                    'msg' => 'You do not have permission to access this resource.',
                    'code' => 403
                ];
            }
            $order->delete();
            return ['status' => true];

        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return [
                'status' => false,
                'msg' => 'A server error has occurred',
                'code' => 500
            ];
        }
    }
}
