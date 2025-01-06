<?php

namespace App\Policies;

use App\Models\User\User;
use App\Models\Order\Order;

class OrderPolicy
{
    /**
     * Create a new policy instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Determine if the user can view the orders.
     * @param \App\Models\User\User $user
     * @return bool
     */
    public function viewOrdersUser(User $user)
    {
        return $user->hasRole('customer');
    }

    /**
     * Determine if the admin can view the orders.
     * @param \App\Models\User\User $user
     * @return bool
     */
    public function viewOrdersAdmin(User $user)
    {
        return $user->hasRole('admin');
    }

    /**
     * Determine if the user can destroy the order.
     *
     * @param \App\Models\User\User $user
     * @param \App\Models\Order\Order $order
     * @return bool
     */
    public function destroy(User $user, Order $order)
    {
        return $user->id === $order->user_id;
    }

    /**
     * Determine if the user can show the order.
     *
     * @param \App\Models\User\User $user
     * @param \App\Models\Order\Order $order
     * @return bool
     */
    public function show(User $user, Order $order)
    {
        return $user->id === $order->user_id;
    }

}
