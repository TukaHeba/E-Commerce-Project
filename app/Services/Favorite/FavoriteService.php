<?php

namespace App\Services\Favorite;

use Exception;
use App\Models\User\User;
use Illuminate\Http\Request;
use App\Models\Product\Product;
use App\Traits\CacheManagerTrait;
use Illuminate\Database\Eloquent\Collection;

class FavoriteService
{
    use CacheManagerTrait;
    private $groupe_key_cache = 'products_cache_keys';
    /**
     * Add a new product to user favorite
     *
     * @param Product $product The validated data to add favorite product.
     * @return null
     */

    public function storeFavorite(Product $product)
    {
        $user = User::findOrFail(auth()->user()->id);
        $result = $user->favoriteProducts()->syncWithoutDetaching($product->id);
        if (empty($result['attached'])) {
            throw new Exception("Product is already in favorites");
        }
        $this->clearCacheGroup($this->groupe_key_cache);
    }

    /**
     * show user favorite products
     *
     * @return  Collection user with its favorite products
     */

    public function showFavorites()
    {
        $user = User::findOrFail(auth()->id());
        $user_favorite_products = $user->favoriteProducts()->get();
        if ($user_favorite_products->isEmpty()) {
            throw new Exception("You do not have favorite products. Add some products to favorites.", 404);
        }
        return $user_favorite_products;
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
        if ($user->favoriteProducts()->where('product_id', $product->id)->doesntExist()) {
            throw new Exception("You do not have permission to access this resource.");
        }
        $user->favoriteProducts()->detach($product->id);
        $this->clearCacheGroup($this->groupe_key_cache);
    }
}
