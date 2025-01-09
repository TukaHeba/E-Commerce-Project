<?php

namespace App\Policies\Order;

use App\Models\User\User;
use App\Models\Order\Order;

/**
 * Class OrderPolicy
 *
 * This policy defines the authorization rules for actions on the Order model.
 * It ensures that users have the appropriate roles or ownership to perform actions on orders.
 *
 * @package App\Policies\Order
 */
class OrderPolicy
{
    /**
     * Determine if the user can view the order.
     *
     * @param User $user The authenticated user.
     * @param Order $order The order being viewed.
     * @return bool True if the user owns the order or has the 'admin' or 'sales manager' role.
     */
    public function show(User $user, Order $order): bool
    {
        return $user->id === $order->user_id || $user->hasRole(['admin', 'sales manager']);
    }

    /**
     * Determine if the user can update the order.
     *
     * @param User $user The authenticated user.
     * @return bool True if the user has the 'admin' or 'store manager' role.
     */
    public function update(User $user): bool
    {
        return $user->hasRole(['admin', 'store manager']);
    }

    /**
     * Determine if the user can delete the order.
     *
     * @param User $user The authenticated user.
     * @return bool True if the user has the 'admin' role.
     */
    public function delete(User $user): bool
    {
        return $user->hasRole('admin');
    }

    /**
     * Determine if the user can view deleted orders.
     *
     * @param User $user The authenticated user.
     * @return bool True if the user has the 'admin' or 'sales manager' role.
     */
    public function showDeleted(User $user): bool
    {
        return $user->hasRole(['admin', 'sales manager']);
    }

    /**
     * Determine if the user can restore deleted orders.
     *
     * @param User $user The authenticated user.
     * @return bool True if the user has the 'admin' or 'sales manager' role.
     */
    public function restoreDeleted(User $user): bool
    {
        return $user->hasRole(['admin', 'sales manager']);
    }

    /**
     * Determine if the user can force delete the order.
     *
     * @param User $user The authenticated user.
     * @return bool True if the user has the 'admin' role.
     */
    public function forceDeleted(User $user): bool
    {
        return $user->hasRole('admin');
    }
}
