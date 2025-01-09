<?php

namespace App\Policies\Report;

use App\Models\User\User;

/**
 * Class ReportPolicy
 *
 * This policy defines the authorization rules for generating reports.
 * It ensures that only users with specific roles can access different types of reports.
 *
 * @package App\Policies\Report
 */
class ReportPolicy
{
    /**
     * Create a new policy instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Determine if the user can view a report of orders that are late to deliver.
     *
     * @param User $user The authenticated user.
     * @return bool True if the user has the 'admin' or 'sales manager' role.
     */
    public function orderLateToDeliever(User $user)
    {
        return $user->hasRole(['admin', 'sales manager']);
    }

    /**
     * Determine if the user can view a report of products remaining in the cart.
     *
     * @param User $user The authenticated user.
     * @return bool True if the user has the 'admin' or 'store manager' role.
     */
    public function productsRemainingInCart(User $user)
    {
        return $user->hasRole(['admin', 'store manager']);
    }

    /**
     * Determine if the user can view a report of products that are low on stock.
     *
     * @param User $user The authenticated user.
     * @return bool True if the user has the 'admin' or 'store manager' role.
     */
    public function productslowOnStock(User $user)
    {
        return $user->hasRole(['admin', 'store manager']);
    }

    /**
     * Determine if the user can view a report of products that have not been sold.
     *
     * @param User $user The authenticated user.
     * @return bool True if the user has the 'admin' or 'store manager' role.
     */
    public function productsNotSold(User $user)
    {
        return $user->hasRole(['admin', 'store manager']);
    }

    /**
     * Determine if the user can view a report of the best categories.
     *
     * @param User $user The authenticated user.
     * @return bool True if the user has the 'admin', 'store manager', or 'sales manager' role.
     */
    public function bestCategories(User $user)
    {
        return $user->hasRole(['admin', 'store manager', 'sales manager']);
    }

    /**
     * Determine if the user can view a report of the best-selling products.
     *
     * @param User $user The authenticated user.
     * @return bool True if the user has the 'admin', 'store manager', or 'sales manager' role.
     */
    public function bestSellingProducts(User $user)
    {
        return $user->hasRole(['admin', 'store manager', 'sales manager']);
    }

    /**
     * Determine if the user can view a report of the countries with the highest orders.
     *
     * @param User $user The authenticated user.
     * @return bool True if the user has the 'admin', 'store manager', or 'sales manager' role.
     */
    public function countriesWithHighestOrders(User $user)
    {
        return $user->hasRole(['admin', 'store manager', 'sales manager']);
    }
}

