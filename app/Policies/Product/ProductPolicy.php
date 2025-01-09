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
     * Determine if the user can create a new product.
     *
     * @param User $user The authenticated user.
     * @return bool True if the user has the 'admin' or 'store manager' role.
     */
    public function store(User $user): bool
    {
        return $user->hasRole(['admin', 'store manager']);
    }

    /**
     * Determine if the user can update a product.
     *
     * @param User $user The authenticated user.
     * @return bool True if the user has the 'admin' or 'store manager' role.
     */
    public function update(User $user): bool
    {
        return $user->hasRole(['admin', 'store manager']);
    }

    /**
     * Determine if the user can delete a product (soft delete).
     *
     * @param User $user The authenticated user.
     * @return bool True if the user has the 'admin' or 'store manager' role.
     */
    public function delete(User $user): bool
    {
        return $user->hasRole(['admin', 'store manager']);
    }

    /**
     * Determine if the user can view soft-deleted products.
     *
     * @param User $user The authenticated user.
     * @return bool True if the user has the 'admin' or 'sales manager' role.
     */
    public function showDeleted(User $user): bool
    {
        return $user->hasRole(['admin', 'sales manager']);
    }

    /**
     * Determine if the user can restore soft-deleted products.
     *
     * @param User $user The authenticated user.
     * @return bool True if the user has the 'admin' or 'sales manager' role.
     */
    public function restoreDeleted(User $user): bool
    {
        return $user->hasRole(['admin', 'sales manager']);
    }

    /**
     * Determine if the user can permanently delete a product.
     *
     * @param User $user The authenticated user.
     * @return bool True if the user has the 'admin' role.
     */
    public function forceDeleted(User $user): bool
    {
        return $user->hasRole('admin');
    }

    /**
     * Determine if the user can retrieve the largest quantity sold for a product.
     *
     * @param User $user The authenticated user.
     * @return bool True if the user has the 'admin', 'sales manager', or 'store manager' role.
     */
    public function largestQuantitySold(User $user): bool
    {
        return $user->hasRole(['admin', 'sales manager', 'store manager']);
    }

}
