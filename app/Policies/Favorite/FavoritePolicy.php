<?php

namespace App\Policies\Favorite;

use App\Models\User\User;
use App\Models\Favorite\Favorite;

/**
 * Class FavoritePolicy
 *
 * This policy defines the authorization rules for actions on the Favorite model.
 * It ensures that users can only view or delete their own favorites.
 *
 * @package App\Policies\Favorite
 */
class FavoritePolicy
{
    /**
     * Create a new policy instance.
     *
     * The constructor initializes the policy instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Determine if the user can view a specific favorite.
     *
     * @param User $user The authenticated user.
     * @param Favorite $favorite The favorite being viewed.
     * @return bool True if the favorite belongs to the authenticated user; otherwise, false.
     */
    public function show(User $user, Favorite $favorite): bool
    {
        return $user->id === $favorite->user_id;
    }

    /**
     * Determine if the user can delete a specific favorite.
     *
     * @param User $user The authenticated user.
     * @param Favorite $favorite The favorite being deleted.
     * @return bool True if the favorite belongs to the authenticated user; otherwise, false.
     */
    public function destroy(User $user, Favorite $favorite): bool
    {
        return $user->id === $favorite->user_id;
    }
}
