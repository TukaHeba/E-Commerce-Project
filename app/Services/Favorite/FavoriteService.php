<?php

namespace App\Services\Favorite;

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

    public function storeFavorite(Product $product, $id)
    {
        $user = User::findOrFail($id);
        $result = $user->favoriteProducts()->syncWithoutDetaching($product->id);
        $this->clearCacheGroup($this->groupe_key_cache);
        if (!empty($result['attached'])) {
            return true;
        }
    }

    /**
     * show user favorite products
     *
     * @return  Collection user with its favorite products
     */

    public function showFavorites()
    {
        $user = User::findOrFail(auth()->id());
        return  $user->favoriteProducts()->get();
    }

    /**
     * remove product from user's favorite products
     *
     * @param Request $request
     * @return null
     */

    public function destroyFavorite($product , $id)
    {
        $user = User::findOrFail($id);
        if ($user->favoriteProducts()->where('product_id', $product->id)->exists()) {
            $user->favoriteProducts()->detach($product->id);
            $this->clearCacheGroup($this->groupe_key_cache);
            return true;
        }
    }
}
