<?php


namespace App\Services\Product;

use Illuminate\Http\Request;
use App\Models\Product\Product;
use App\Models\Category\SubCategory;
use App\Models\Category\MainCategory;
use Illuminate\Support\Facades\Cache;
use App\Http\Resources\ProductResource;
use App\Models\Photo\Photo;
use App\Services\Photo\PhotoService;
use Illuminate\Http\Exceptions\HttpResponseException;

class ProductService
{
    protected PhotoService $photoService ;
    public function __construct(PhotoService $photoService){
        $this->photoService = $photoService ;
    }

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
     */
    public function getProductsByCategory($request){
        $cache_key = $this->generateCacheKey('products_by_category', array_filter([
            'sub_category_id' => $request->subCategoryId,
            'main_category_id' => $request->mainCategoryId,
        ]));
        $this->addCasheKey($cache_key);

        return Cache::remember($cache_key, now()->addHour(), function () use ($request) {
            return Product::with(['mainCategory', 'subCategory'])
                ->byCategory($request)
                ->available()
                ->paginate(10);
        });
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
            return Product::latestProducts()->available()->with(['mainCategory', 'subCategory'])->paginate(10);
        });
    }

    /**
     * Retrieve filtered products with caching and pagination based on query parameters.
     *
     * @param \Illuminate\Http\Request $request - The HTTP request containing filter parameters.
     * @return \Illuminate\Pagination\LengthAwarePaginator - Returns a paginated list of filtered products.
     */
    public function getProductsWithFilter(Request $request)
    {
        $request->merge(['user_id' => auth()->check() ? auth()->id() : null]);

        $cache_key = $this->generateCacheKey('products_filter', $request->all());
        $this->addCasheKey($cache_key);

        return Cache::remember($cache_key, now()->addHour(), function () use ($request) {

            return Product::filterProducts($request)->withAvg('ratings', 'rating')->paginate(10);
        });
    }
    /**
     * Retrieve hot selling products with caching and pagination .
     * @param mixed $request
     * @return mixed
     */
    public function getBestSellingProducts()
    {
        $cache_key = 'best_selling_products';
        $this->addCasheKey($cache_key);

        return Cache::remember($cache_key, now()->addHour(), function () {
            return Product::bestSelling()->available()->paginate(10);
        });
    }
    /**
     * Retrieve Products User May Like
     * @throws \Illuminate\Http\Exceptions\HttpResponseException
     * @return mixed
     */
    public function getProductsUserMayLike()
    {
        $user_id = auth()->id();
        $cache_key = $this->generateCacheKey('products_may_like_by:', ['user' => $user_id]);
        $this->addCasheKey($cache_key);

        return Cache::remember($cache_key, now()->addHour(), function () use ($user_id) {
            return Product::mayLikeProducts($user_id)->available()->paginate(10);
        });
    }
    /**
     * Retrieve Top Rated Products
     * @param int $limit
     * @return mixed
     */
    public function getTopRatedProducts(int $limit = 10)
    {
        $cache_key = 'top_rating_products';
        $this->addCasheKey($cache_key);

        return Cache::remember($cache_key, now()->addHour(), function () use ($limit) {
            return Product::topRated($limit)->with(['mainCategory', 'subCategory'])->available()->paginate(30);
        });
    }

    public function storeProduct($data , $photos)
    {
        $product = Product::create($data);
        $this->photoService->storeMultiplePhotos($photos , $product);

        return $product ;
    }
    public function updateProduct($product, $data , $photoForDelete = [])
    {
        $product->update($data);
        foreach($photoForDelete as $filePath){
            // Retrieve the photo based on its path
            $photo = Photo::where('photo_path',$filePath)->first();
            if($photo){
                $this->photoService->deletePhoto($photo->photo_path);

                // Delete photo from the Database
                $photo->delete();
            }
        }
        $product->save();
        return $product;
    }

    public function showLargestQuantitySold($name)
    {
        $product = Product::where('name', 'like', '%' . $name . '%')->first();
        if ($product) {
            $largestOrderItem = $product->largestQuantitySoldByName($name)->first();
            if ($largestOrderItem) {
                return [
                    'Order Id' => $largestOrderItem->order_id,
                    'Product Id' => $largestOrderItem->product_id,
                    'Quantity' => $largestOrderItem->quantity
                ];
            }
        }
    }

}

