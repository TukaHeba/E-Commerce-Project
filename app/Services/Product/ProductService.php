<?php

namespace App\Services\Product;

use App\Models\Photo\Photo;
use Illuminate\Http\Request;
use App\Models\Product\Product;
use App\Traits\CacheManagerTrait;
use App\Services\Photo\PhotoService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Http\Exceptions\HttpResponseException;

class ProductService
{
    use CacheManagerTrait;
    private $groupe_key_cache = 'products_cache_keys';
    protected PhotoService $photoService;

    /**
     * Constructor to inject PhotoService dependency.
     *
     * @param PhotoService $photoService
     */
    public function __construct(PhotoService $photoService)
    {
        $this->photoService = $photoService;
    }

    /**
     * Retrieve products by category with caching and pagination.
     *
     * @param Request $request The HTTP request containing category filter parameters.
     * @return \Illuminate\Pagination\LengthAwarePaginator A paginated list of products in the specified category.
     */
    public function getProductsByCategory($request)
    {
        $cache_key = $this->generateCacheKey('products_by_category', array_filter([
            'sub_category_id' => $request->subCategoryId,
            'main_category_id' => $request->mainCategoryId,
        ]));
        $this->addCacheKey($this->groupe_key_cache, $cache_key);

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
     * @return \Illuminate\Pagination\LengthAwarePaginator A paginated list of the latest products.
     */
    public function getLatestProducts()
    {
        $cache_key = 'latest_products';
        $this->addCacheKey($this->groupe_key_cache, $cache_key);
        return Cache::remember($cache_key, now()->addHour(), function () {
            return Product::latestProducts()->available()->with(['mainCategory', 'subCategory'])->paginate(10);
        });
    }

    /**
     * Retrieve filtered products with caching and pagination based on query parameters.
     *
     * @param Request $request The HTTP request containing filter parameters.
     * @return \Illuminate\Pagination\LengthAwarePaginator A paginated list of filtered products.
     */
    public function getProductsWithFilter(Request $request)
    {
        $request->merge(['user_id' => auth()->check() ? auth()->id() : null]);
        $cache_key = $this->generateCacheKey('products_filter', $request->all());
        $this->addCacheKey($this->groupe_key_cache, $cache_key);
        return Cache::remember($cache_key, now()->addHour(), function () use ($request) {
            return Product::filterProducts($request)->paginate(10);
        });
    }

    /**
     * Retrieve best-selling products with caching and pagination.
     *
     * @param mixed $request The HTTP request for fetching best-selling products.
     * @return mixed A paginated list of the best-selling products.
     */
    public function getBestSellingProducts()
    {
        $cache_key = 'best_selling_products';
        $this->addCacheKey($this->groupe_key_cache, $cache_key);
        return Cache::remember($cache_key, now()->addHour(), function () {
            return Product::bestSelling('product_with_total_sold')
                ->available()
                ->paginate(10);
        });
    }
    /**
     * Retrieve offer products with caching and pagination.
     *
     * @param mixed $request The HTTP request for fetching best-selling products same month from last year.
     * @return mixed A paginated list of products.
     */
    public function getOfferProducts()
    {
        $cache_key = 'offer_products';
        $this->addCacheKey($this->groupe_key_cache, $cache_key);
        return Cache::remember($cache_key, now()->addMonth(), function () {
            return Product::bestSelling('offer')
                ->available()
                ->paginate(10);
        });
    }

    /**
     * Retrieve products the user may like based on their preferences.
     *
     * @throws HttpResponseException If there is an issue fetching products.
     * @return mixed A paginated list of products the user may like.
     */
    public function getProductsUserMayLike()
    {
        $user_id = auth()->id();
        $cache_key = $this->generateCacheKey('products_may_like_by:', ['user' => $user_id]);
        $this->addCacheKey($this->groupe_key_cache, $cache_key);
        return Cache::remember($cache_key, now()->addHour(), function () use ($user_id) {
            return Product::mayLikeProducts($user_id)->available()->paginate(10);
        });
    }

    /**
     * Retrieve the top-rated products with caching and pagination.
     *
     * @param int $limit The maximum number of products to return.
     * @return mixed A paginated list of top-rated products.
     */
    public function getTopRatedProducts(int $limit = 10)
    {
        $cache_key = 'top_rating_products';
        $this->addCacheKey($this->groupe_key_cache, $cache_key);
        return Cache::remember($cache_key, now()->addHour(), function () use ($limit) {
            return Product::topRated($limit)->with(['mainCategory', 'subCategory'])->available()->paginate(30);
        });
    }

    /**
     * Store a new product along with its associated photos.
     *
     * @param array $data The product data.
     * @param array $photos The photos to associate with the product.
     * @return Product The created product.
     */
    public function storeProduct($data, $photos)
    {
        $product = Product::create($data);
        $this->photoService->storeMultiplePhotos($photos, $product);  // Store product photos.
        $this->clearCacheGroup($this->groupe_key_cache);
        return $product;
    }

    /**
     * Update an existing product, including removing photos if specified.
     *
     * @param Product $product The product to update.
     * @param array $data The new product data.
     * @param array $photoForDelete The paths of photos to delete.
     * @return Product The updated product.
     */
    public function updateProduct($product, $data, $photoForDelete = [])
    {
        $product->update($data);
        foreach ($photoForDelete as $filePath) {
            $photo = Photo::where('photo_path', $filePath)->first();
            if ($photo) {
                $this->photoService->deletePhoto($photo->photo_path);
                $photo->delete();
            }
        }
        $this->clearCacheGroup($this->groupe_key_cache);
        $product->save();

        return $product;
    }

    /**
     * Show the largest quantity of a product sold by name.
     *
     * @param string $name The product name.
     * @return array|null The order details if found, otherwise null.
     */
    public function showLargestQuantitySold($name)
    {
        $cache_key = "Largest_Quantity_Sold";
        $this->addCacheKey($this->groupe_key_cache, $cache_key);
        $product = Product::where('name', 'like', '%' . $name . '%')->first();
        if ($product) {
            $largestOrderItem = $product->largestQuantitySoldByName($name)->first();
            if ($largestOrderItem) {
                return Cache::remember($cache_key, now()->addWeek(), function () use ($largestOrderItem) {
                    return [
                        'Order Id' => $largestOrderItem->order_id,
                        'Product Id' => $largestOrderItem->product_id,
                        'Quantity' => $largestOrderItem->quantity
                    ];
                });
            }
        }
    }
}
