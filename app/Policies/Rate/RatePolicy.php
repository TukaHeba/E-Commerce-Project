<?php

namespace App\Policies\Rate;

use App\Models\Rate\Rate;
use App\Models\User\User;

/**
 * Class RatePolicy
 *
 * This policy defines the authorization rules for actions on the Rate model.
 * It ensures that users can only perform actions on their own ratings.
 *
 * @package App\Policies\Rate
 */
class RatePolicy
{
    /**
     * Create a new policy instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Determine if the user can update the rate.
     *
     * @param User $user The authenticated user.
     * @param Rate $rate The rate being updated.
     * @return bool True if the user owns the rate.
     */
    public function update(User $user, Rate $rate): bool
    {
        return $user->id == $rate->user_id;
    }

    /**
     * Determine if the user can delete the rate.
     *
     * @param User $user The authenticated user.
     * @param Rate $rate The rate being deleted.
     * @return bool True if the user owns the rate.
     */
    public function delete(User $user, Rate $rate): bool
    {
        return $user->id == $rate->user_id;
    }
}
