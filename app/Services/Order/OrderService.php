<?php

namespace App\Services\Order;

use App\Models\User\User;
use App\Models\Order\Order;
use App\Traits\CacheManagerTrait;
use App\Jobs\SendOrderTrackingEmail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class OrderService
{
    use CacheManagerTrait;
    private $groupe_key_cache = 'orders_cache_keys';

    /**
     * List of orders related to user
     *
     * @param mixed $request
     * @return mixed
     */
    public function getOrdersUser($request)
    {
        $cache_key = $this->generateCacheKey('user-orders', $request->all());
        $this->addCacheKey($this->groupe_key_cache, $cache_key);

        $orders = Cache::remember($cache_key . Auth::id(), 1200, function () use ($request) {
            return Order::byFilters($request)->where('user_id', Auth::id())->paginate(10);
        });
        return $orders;
    }

    /**
     * List of orders related to admin
     *
     * @param mixed $request
     * @return mixed
     */
    public function getOrdersAdmin($request)
    {
        $cache_key = $this->generateCacheKey('user-orders', $request->all());
        $this->addCacheKey($this->groupe_key_cache, $cache_key);

        $orders = Cache::remember($cache_key, 1200, function () use ($request) {
            return Order::byFilters($request)->paginate(10);
        });
        return $orders;
    }

    /**
     * Update order status
     *
     * @param \App\Models\Order\Order $order
     * @param array $data
     * @return Order
     */
    public function updateOrder(Order $order, array $data)
    {
        $order->update(array_filter($data));

        $user = User::where('id', $order->user_id)->first();
        SendOrderTrackingEmail::dispatch($user->email, $user->first_name, $order->id, $order->status);
        $this->clearCacheGroup($this->groupe_key_cache);
        return $order;
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
    public function getDeletedOrders($request)
    {
        $cache_key = 'deleted-orders';
        $this->addCacheKey($this->groupe_key_cache, $cache_key);
        $deletedOrders = Cache::remember($cache_key, 1200, function () use ($request) {
            return Order::onlyTrashed()
                ->byFilters($request)
                ->paginate(10);
        });
        return $deletedOrders;
    }
}
