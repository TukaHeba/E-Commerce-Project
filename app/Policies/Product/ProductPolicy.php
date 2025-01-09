<?php

namespace App\Policies\Product;

use App\Models\User\User;

class ProductPolicy
{
    /**
     * Create a new policy instance.
     */
    public function __construct()
    {
        //
    }

      /**
     * Determine if the user can store the order.
     *
     * @param User $user The authenticated user.
     * @return bool True if the user has the 'admin' or 'store manager' role.
     */
    public function store(User $user): bool
    {
        return $user->hasRole(['admin', 'store manager']);
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
     * @return bool True if the user has the 'admin' or 'sales manager' role.
     */
    public function delete(User $user): bool
    {
        return $user->hasRole(['admin', 'store manager']);
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
