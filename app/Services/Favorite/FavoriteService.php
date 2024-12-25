<?php

namespace App\Services\Favorite;

use App\Models\Product\Product;
use App\Models\User\User;
use Illuminate\Http\Request;

class FavoriteService
{
    /**
     * Retrieve all favorites with pagination.
     *
     * @return \Illuminate\Pagination\LengthAwarePaginator Paginated favorites.
     */
    public function getFavorites()
    {
        $users = User::with('favoriteProducts')->get();
        return $users->paginate(10);
    }

    /**
     * Add a new product to user favorite
     *
     * @param array $data The validated data to add favorite product.
     * @return null
     */
    public function storeFavorite(array $data)
    {
        $user = User::findOrFail($data['user_id']);
        $user->favoriteProducts()->attach($data['product_id']);
    }
    /**
     * show user favorite products
     *
     * @param User $user
     * @return  User with its favorite products
     */

    public function showFavorites(User $user)
    {
        return  $user->load('favoriteProducts');
    }
    /**
     * show users who favor specific product
     *
     * @param Product $product
     * @return Product with users who favorited it
     */

     public function usersFavoringProduct(Product $product)
     {
         return  $product->load('favoredBy');
     }
      /**
     * remove product from user's favorite products
     *
     * @param Request $request
     * @return null
     */

    public function destroyFavorite($request)
    {
        $user = User::findOrFail($request->user_id);
        $user->favoriteProducts()->detach($request->product_id);
    }
}
