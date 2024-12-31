<?php

namespace App\Services\Order;

use App\Models\User\User;
use App\Models\Order\Order;
use App\Jobs\SendNotification;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class OrderService
{
    /**
     * List of orders related to user
     * @param array $data
     * @return mixed
     */
    public function getOrders(array $data)
    {
        $orders = Cache::remember('orders_' . Auth::id(), 1200, function () use ($data) {
            return Order::byFilters($data)->where('user_id', Auth::id())->paginate(10);
        });
        return $orders;
    }

    /**
     * Update order status
     * @param \App\Models\Order\Order $order
     * @param array $data
     * @return Order
     */
    public function updateOrder(Order $order, array $data)
    {
        $order->update(array_filter($data));
        
        $user = User::where('id', $order->user_id)->first();
        SendNotification::dispatch($user->email, $user->first_name, $order->id, $order->status);

        return $order;
    }

    /**
     * Soft delete order
     * @param \App\Models\Order\Order $order
     * @return array
     */
    public function destroyOrder(Order $order)
    {
        if ($order && $order->user_id !== Auth::id()) {
            return [
                'status' => false,
                'msg' => 'You do not have permission to access this resource.',
                'code' => 403
            ];
        }
        $order->delete();
        return ['status' => true];
    }

    /**
     * List of deleted orders related to user
     * @param array $data
     * @return mixed
     */
    public function getDeletedOrders(array $data)
    {
        $deletedOrders = Cache::remember('deleted_orders_' . Auth::id(), 1200, function () use ($data) {
            return Order::onlyTrashed()
                ->byFilters($data)
                ->where('user_id', Auth::id())
                ->paginate(10);
        });
        return $deletedOrders;
    }
}
