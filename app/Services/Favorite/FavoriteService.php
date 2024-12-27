<?php

namespace App\Services\Favorite;

use auth;
use App\Models\User\User;
use Illuminate\Http\Request;
use App\Models\Product\Product;


class FavoriteService
{
    /*
     * Add a new product to user favorite
     *
     * @param Product $product The validated data to add favorite product.
     * @return null
     */

    public function storeFavorite(Product $product)
    {
        $user = User::findOrFail(auth()->user()->id);
        $user->favoriteProducts()->attach($product->id);
    }
    /*
     * show user favorite products
     *
     * @return  User with its favorite products
     */

    public function showFavorites()
    {
        $user = User::findOrFail(auth()->user()->id);
        return  $user->load('favoriteProducts');
    }
      /**
     * remove product from user's favorite products
     *
     * @param Request $request
     * @return null
     */

    public function destroyFavorite($product)
    {
        $user = User::findOrFail(auth()->user()->id);
        $user->favoriteProducts()->detach($product->id);
    }
}
