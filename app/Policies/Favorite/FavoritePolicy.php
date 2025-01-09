<?php

namespace App\Policies\Favorite;

use App\Models\User\User;
use App\Models\Product\Product;
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
     * Class FavoritePolicy
     *
     * This policy defines the authorization rules for actions on the Favorite model.
     * It ensures that users can only view or delete their own favorite products.
     */
    public function __construct()
    {
        //
    }

    /**
     * Determine if the user can delete a product from their favorites.
     *
     * @param User $user The authenticated user.
     * @param Product $product The product being removed from favorites.
     * @return bool True if the product belongs to the user's favorites.
     */
    public function destroy(User $user, Product $product): bool
    {
        return $user->favoriteProducts()->where('product_id', $product->id)->exists();
    }
}
