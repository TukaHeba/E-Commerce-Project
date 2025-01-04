<?php

namespace App\Services\Order;

use App\Models\User\User;
use App\Models\Order\Order;
use App\Jobs\SendNotification;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class OrderService
{
    /**
     * List of orders related to user
     * @param mixed $request
     * @return mixed
     */
    public function getOrdersUser($request)
    {
        $orders = Cache::remember('orders_' . Auth::id(), 1200, function () use ($request) {
            return Order::byFilters($request)->where('user_id', Auth::id())->paginate(10);
        });
        return $orders;
    }

    /**
     * List of orders related to admin
     * @param mixed $request
     * @return mixed
     */
    public function getOrdersAdmin($request)
    {
        $orders = Cache::remember('orders_' . Auth::id(), 1200, function () use ($request) {
            return Order::byFilters($request)->paginate(10);
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
     * List of deleted orders related to user
     * @param mixed $request
     * @return mixed
     */
    public function getDeletedOrdersUser($request)
    {
        $deletedOrders = Cache::remember('deleted_orders_' . Auth::id(), 1200, function () use ($request) {
            return Order::onlyTrashed()
                ->byFilters($request)
                ->where('user_id', Auth::id())
                ->paginate(10);
        });
        return $deletedOrders;
    }

    /**
     * Fetch the tracking history associated with the specified order
     *
     * @param \App\Models\Order\Order $order
     * @return Order
     */
    public function getOrderTracking(Order $order)
    {
        $order->load('orderTrackings');
        return $order;
    }

    /**
     * List of deleted orders related to admin
     * @param mixed $request
     * @return mixed
     */
    public function getDeletedOrdersAdmin($request)
    {
        $deletedOrders = Cache::remember('deleted_orders_' . Auth::id(), 1200, function () use ($request) {
            return Order::onlyTrashed()
                ->byFilters($request)
                ->paginate(10);
        });
        return $deletedOrders;
    }

    /**
     * Get oldest order related to user
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     * @return mixed
     */
    public function getOldestOrder()
    {
        $user = Auth::user();
        if (!$oldestOrder = $user->oldestOrder) {
            throw new ModelNotFoundException();
        }
        return $oldestOrder;
    }

    /**
     * Get latest order related to user
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     * @return mixed
     */
    public function getLatestOrder()
    {
        $user = Auth::user();
        if (!$latestOrder = $user->latestOrder) {
            throw new ModelNotFoundException();
        }
        return $latestOrder;
    }
}
