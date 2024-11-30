<?php
namespace App\Service\Product;

use Exception;
use Illuminate\Http\Request;
use App\Models\Product\Product;
use App\Models\Category\Category;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use App\Http\Requests\Product\UpdateProductRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class ProductService{

    /**
     * Generate a cache key dynamically based on parameters.
     *
     * @param string $base
     * @param array $params
     * @return string
     */
    private function generateCacheKey(string $base, array $params): string
    {
        return $base . ':' . http_build_query($params);
    }
    /**
     * Store cache keys to clear it later
     * @param mixed $cache_key
     * @return void
     */
    public function addCasheKey($cache_key)
    {
        $cache_keys = Cache::get('product_cache_keys', []);
        if (!in_array($cache_key, $cache_keys)) {
            $cache_keys[] = $cache_key;
            Cache::put('task_cache_keys', $cache_keys);
        }
    }
    /**
     * clear product cache manually
     * @return void
     */
    public function clearProductCache()
    {
        // Get the cache keys list
        $cacheKeys = Cache::get('product_cache_keys', []);

        // Loop through and forget each cached product entry
        foreach ($cacheKeys as $cacheKey) {
            Cache::forget($cacheKey);
        }

        // Optionally clear the cache key list itself
        Cache::forget('task_cache_keys');
    }

    /**
     * Retrieve products by category with caching and pagination.
     *
     * @param int $category_id - The ID of the category to filter products by.
     * @return \Illuminate\Pagination\LengthAwarePaginator - Returns a paginated list of products within the specified category.
     * @throws \Illuminate\Http\Exceptions\HttpResponseException - Throws an exception if the category is not found or if there is a server error.
     */
    public function getProductsByCategory($category_id)
    {
        try {
            // Find the category by its ID or throw a 404 error if not found.
            $category = Category::findOrFail($category_id);

            // Generate a unique cache key based on the category name.
            $cache_key = $this->generateCacheKey('products_by_category', ['category' => $category->name]);

            // Add the cache key to product_cache_keys
            $this->addCasheKey($cache_key);

            // Retrieve products from the cache or fetch from the database if not cached.
            // The cache duration is set to 1 hour.
            return Cache::remember($cache_key, now()->addHour(), function () use ($category_id) {
                // Fetch products by category, filter for availability, and paginate the results (10 items per page).
                return Product::byCategory($category_id)->available()->paginate(10);
            });

        } catch (ModelNotFoundException $e) {
            // Log the error and return a 404 response if the category is not found.
            Log::error('Category not found: ' . $e->getMessage());
            throw new HttpResponseException(response()->json(['message' => 'Category not found!'], 404));

        } catch (Exception $e) {
            // Log any other errors and return a 500 response for server errors.
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
        // Define a unique cache key for the latest products.
        $cache_key = 'latest_products';

        // Add the cache key to product_cache_keys
        $this->addCasheKey($cache_key);

        // Retrieve products from the cache or fetch from the database if not cached.
        // The cache duration is set to 1 hour.
        return Cache::remember($cache_key, now()->addHour(), function () {
            // Fetch the latest products, ensuring only available products are retrieved.
            // Results are paginated, returning 10 items per page.
            return Product::latestProducts()->available()->paginate(10);
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

            // Generate a unique cache key based on the filter parameters.
            $cache_key = $this->generateCacheKey('products_filter', compact('price', 'name', 'category_id', 'latest'));

            // Add the cache key to product_cache_keys
            $this->addCasheKey($cache_key);

            // Retrieve products from the cache or fetch from the database if not cached.
            // The cache duration is set to 1 hour.
            return Cache::remember($cache_key, now()->addHour(), function () use ($price, $name, $category_id, $latest) {
                // Apply filters to the products query and paginate the results.
                return Product::filterProducts($price, $name, $category_id, $latest)->paginate(10);
            });
        } catch (Exception $e) {
            // Log any other errors that occur during product retrieval.
            Log::error('Error retrieving products: ' . $e->getMessage());

            // Return a 500 response indicating a server error.
            throw new HttpResponseException(response()->json(['message' => 'Server error'], 500));
        }
    }

    public function getProducts(Request $request){
        try{
            $products = Product::with('category')->paginate(5);
            return $products ;
        }catch(AccessDeniedHttpException){
            throw new AccessDeniedHttpException();
        }catch(Exception $e){
            throw new Exception($e->getMessage());
        }
    }
    public function storeProduct($data){
        try{
            return Product::create($data);
        }catch(ModelNotFoundException){
            throw new ModelNotFoundException();
        }catch(Exception){
            throw new Exception();
        }catch(AccessDeniedHttpException){
            throw new AccessDeniedHttpException();
        }catch(Exception){
            throw new Exception();
        }
    }
    public function updateProduct($product,$data){
        try{
            $product->update($data);
            $product->save();
            return $product ;
        }catch(ModelNotFoundException){
            throw new ModelNotFoundException();
        }catch(Exception){
            throw new Exception();
        }
    }

}
