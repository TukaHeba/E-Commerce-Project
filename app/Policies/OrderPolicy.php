<?php

namespace App\Policies;

use App\Models\User\User;

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
}
