<?php

namespace App\Services\Product;

use Exception;
use Illuminate\Http\Request;
use App\Models\Product\Product;
use App\Models\Category\Category;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use App\Http\Requests\Product\UpdateProductRequest;
use App\Models\Category\SubCategory;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class ProductService
{
    /**
     * Generate a unique cache key using a base string and parameters.
     *
     * @param string $base   The base string for the cache key.
     * @param array  $params An array of parameters to include in the key.
     * @return string The generated cache key.
     */
    private function generateCacheKey(string $base, array $params): string
    {
        return $base . ':' . http_build_query($params);
    }

    /**
     * Add a cache key to the list of keys for tracking and clearing later.
     *
     * Ensures that the provided key is stored in a centralized list of cache keys. 
     * If the key already exists in the list, it will not be added again.
     *
     * @param string $cache_key The cache key to add.
     * @return void
     */
    public function addCasheKey(string $cache_key)
    {
        $cache_keys = Cache::get('product_cache_keys', []);

        if (!in_array($cache_key, $cache_keys)) {
            $cache_keys[] = $cache_key;
            Cache::put('product_cache_keys', $cache_keys);
        }
    }

    /**
     * Clear all product cache keys.
     *
     * @return void
     */
    public function clearProductCache()
    {
        $cacheKeys = Cache::get('product_cache_keys', []);
        foreach ($cacheKeys as $cacheKey) {
            Cache::forget($cacheKey);
        }
        Cache::forget('product_cache_keys');
    }

    /**
     * Retrieve products by category with caching and pagination.
     *
     * @param int $sub_category_id - The ID of the category to filter products by.
     * @return \Illuminate\Pagination\LengthAwarePaginator - Returns a paginated list of products within the specified category.
     * @throws \Illuminate\Http\Exceptions\HttpResponseException - Throws an exception if the category is not found or if there is a server error.
     */
    public function getProductsByCategory($sub_category_id)
    {
        try {
            $category = SubCategory::findOrFail($sub_category_id);
            $cache_key = $this->generateCacheKey('products_by_category', ['category' => $category->sub_category_name]);
            $this->addCasheKey($cache_key);
            return Cache::remember($cache_key, now()->addHour(), function () use ($sub_category_id) {
                return Product::byCategory($sub_category_id)->bestSelling()->available()->paginate(10);
            });
        } catch (ModelNotFoundException $e) {
            Log::error('Category not found: ' . $e->getMessage());
            throw new HttpResponseException(response()->json(['message' => 'Category not found!'], 404));
        } catch (Exception $e) {
            Log::error('Error retrieving products: ' . $e->getMessage());
            throw new HttpResponseException(response()->json(['message' => 'Server error'], 500));
        }
    }

    /**
     * Retrieve the latest available products with caching and pagination.
     *
     * @return \Illuminate\Pagination\LengthAwarePaginator - Returns a paginated list of the latest products.
     */
    public function getLatestProducts()
    {
        $cache_key = 'latest_products';
        $this->addCasheKey($cache_key);
        return Cache::remember($cache_key, now()->addHour(), function () {
            return Product::latestProducts()->available()->with('category')->paginate(10);
        });
    }

    /**
     * Retrieve filtered products with caching and pagination based on query parameters.
     *
     * @param \Illuminate\Http\Request $request - The HTTP request containing filter parameters.
     * @return \Illuminate\Pagination\LengthAwarePaginator - Returns a paginated list of filtered products.
     *
     * @throws \Illuminate\Http\Exceptions\HttpResponseException - Throws an exception if the category is not found or in case of a server error.
     */
    public function getProductsWithFilter($request)
    {
        try {
            // Extract query parameters for filtering.
            $price = $request->query('price');             // Order by price ('asc' or 'desc').
            $name = $request->query('name');               // Product name or partial name for search.
            $category_id = $request->query('category_id'); // Category ID to filter by.
            $latest = (bool)$request->query('latest');     // Boolean flag to sort by the latest products.
            $user_id = auth()->check() ? auth()->id() : null;   // User id if user logged in get the favourite category products from favourites table

            $cache_key = $this->generateCacheKey('products_filter', compact('price', 'name', 'category_id', 'latest', 'user_id'));
            $this->addCasheKey($cache_key);

            return Cache::remember($cache_key, now()->addHour(), function () use ($price, $name, $category_id, $latest, $user_id) {
                return Product::filterProducts($price, $name, $category_id, $latest, $user_id)->paginate(10);
            });
        } catch (Exception $e) {
            Log::error('Error retrieving products: ' . $e->getMessage());
            throw new HttpResponseException(response()->json(['message' => 'Server error'], 500));
        }
    }
    
    /**
     * Retrieve hot selling products with caching and pagination .
     * @param mixed $request
     * @throws \Illuminate\Http\Exceptions\HttpResponseException
     * @return mixed
     */
    public function getBestSellingProducts()
    {
        try {
            $cache_key = 'best_selling_products';
            $this->addCasheKey($cache_key);

            return Cache::remember($cache_key, now()->addHour(), function () {
                return Product::bestSelling()->available()->paginate(10);
            });
        } catch (Exception $e) {
            Log::error('Error retrieving products: ' . $e->getMessage());
            throw new HttpResponseException(response()->json(['message' => 'Server error'], 500));
        }
    }

    public function getProductsUserMayLike()
    {
        try {
            $user_id = auth()->check() ?  auth()->id()
                :  throw new HttpResponseException(response()->json(['message' => 'User not authenticated'], 401));
            // $user_id = 10;   // to test resuelt without auth // للحذف
            $cache_key = $this->generateCacheKey('products_may_like_by:', ['user' => $user_id]);
            $this->addCasheKey($cache_key);

            return Cache::remember($cache_key, now()->addHour(), function () use ($user_id) {
                return Product::mayLikeProducts($user_id)->available()->paginate(10);
            });
        } catch (Exception $e) {
            Log::error('Error retrieving products: ' . $e->getMessage());
            throw new HttpResponseException(response()->json(['message' => 'Server error'], 500));
        }
    }

    public function storeProduct($data)
    {
        try {
            return Product::create($data);
        } catch (ModelNotFoundException) {
            throw new ModelNotFoundException();
        } catch (Exception) {
            throw new Exception();
        } catch (AccessDeniedHttpException) {
            throw new AccessDeniedHttpException();
        } catch (Exception) {
            throw new Exception();
        }
    }
    public function updateProduct($product, $data)
    {
        try {
            $product->update($data);
            $product->save();
            return $product;
        } catch (ModelNotFoundException) {
            throw new ModelNotFoundException();
        } catch (Exception) {
            throw new Exception();
        }
    }
}
