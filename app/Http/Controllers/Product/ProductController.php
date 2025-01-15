<?php

namespace App\Http\Controllers\Product;

use Illuminate\Http\Request;
use App\Models\Product\Product;
use App\Traits\CacheManagerTrait;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Services\Photo\PhotoService;
use App\Http\Resources\ProductResource;
use App\Services\Product\ProductService;
use App\Http\Requests\Product\StoreProductRequest;
use App\Http\Requests\Product\UpdateProductRequest;
use App\Http\Requests\Photo\StoreMultiplePhotosRequest;

class ProductController extends Controller
{
    use CacheManagerTrait;
    private $groupe_key_cache = 'products_cache_keys';
    protected ProductService $ProductService;
    protected PhotoService $photoService;

    public function __construct(ProductService $ProductService, PhotoService $photoService)
    {
        $this->ProductService = $ProductService;
        $this->photoService = $photoService;
    }

    /**
     * Retrieve a list of products with filtering options.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $products = $this->ProductService->getProductsWithFilter($request);

        if ($products->isEmpty()) {
            return self::error(null, 'No Products matched!', 404);
        }
        return self::paginated($products, ProductResource::class, 'Products retrieved successfully', 200);
    }

    /**
     * Display details of a specific product.
     *
     * @param \App\Models\Product\Product $product
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Product $product): JsonResponse
    {
        return self::success(new ProductResource($product->load(['mainCategory','subCategory','photos'])), 'Product retrieved successfully');
    }

    /**
     * Create a new product and store it in the database.
     *
     * @param \App\Http\Requests\Product\StoreProductRequest $request
     * @param \App\Http\Requests\Photo\StoreMultiplePhotosRequest $storeMultiplePhotosRequest
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function store(StoreProductRequest $request, StoreMultiplePhotosRequest $storeMultiplePhotosRequest): JsonResponse
    {
        $this->authorize('store', Product::class);
        $photos = $storeMultiplePhotosRequest->file('photos');
        $product = $this->ProductService->storeProduct($request->validated(), $photos);
        return self::success([new ProductResource($product['product']) , 'photos'=>$product['photo']],'Product created successfully', 201);
    }

    /**
     * Update the details of an existing product.
     *
     * @param \App\Http\Requests\Product\UpdateProductRequest $request
     * @param \App\Models\Product\Product $product
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function update(UpdateProductRequest $request, Product $product): JsonResponse
    {
        $this->authorize('update', Product::class);
        $deletedPhotos = $request->input('photosDeleted');
        $updatedProduct = $this->ProductService->updateProduct($product, $request->validated(), $deletedPhotos);
        return self::success([new ProductResource($updatedProduct['product']) , 'photos'=>$updatedProduct['photo']],'Product updated successfully', 200);
    }

    /**
     * Remove a product from the database (soft delete).
     *
     * @param \App\Models\Product\Product $product
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Product $product): JsonResponse
    {
        $this->authorize('delete', Product::class);
        $this->ProductService->delete($product->id);
        $this->clearCacheGroup($this->groupe_key_cache);
        return self::success(null, 'Product deleted successfully');
    }

    /**
     * Retrieve all soft-deleted products.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function showDeleted(): JsonResponse
    {
        $this->authorize('showDeleted', Product::class);
        $products = Product::onlyTrashed()->get();
        return self::success($products,'Products retrieved successfully');
    }

    /**
     * Restore a soft-deleted product.
     *
     * @param string $id The ID of the product to restore.
     * @return \Illuminate\Http\JsonResponse
     */
    public function restoreDeleted(string $id): JsonResponse
    {
        $this->authorize('restoreDeleted', Product::class);
        $product = Product::onlyTrashed()->findOrFail($id);
        $product->restore();
        $this->clearCacheGroup($this->groupe_key_cache);
        return self::success(new ProductResource($product),'Product restored successfully',200);
    }

    /**
     * Permanently delete a soft-deleted product.
     *
     * @param string $id The ID of the product to permanently delete.
     * @return \Illuminate\Http\JsonResponse
     */
    public function forceDeleted(string $id): JsonResponse
    {
        $this->authorize('forceDeleted', Product::class);
        Product::onlyTrashed()->findOrFail($id)->forceDelete();
        $this->clearCacheGroup($this->groupe_key_cache);
        return self::success(null, 'Product force deleted successfully');
    }

    /**
     *  Display a listing of the Products filtered By Category
     * @param \Illuminate\Http\Request $request
     * @return JsonResponse
     */
    public function getProductsByCategory(Request $request){
        $products = $this->ProductService->getProductsByCategory($request);
        if ($products->total() === 0) {
            return self::error(null, 'No Products matched!', 404);
        }
        return self::paginated($products, ProductResource::class,'Products retrieved successfully', 200);
    }

    /**
     * Display a listing of the latest Products
     * @return mixed
     */
    public function getLatestProducts()
    {
        $products = $this->ProductService->getLatestProducts();
        if ($products->isEmpty()) {
            return self::error(null, 'No Products matched!', 404);
        }
        return self::paginated($products, ProductResource::class, 'Products retrieved successfully', 200);
    }
    /**
     * Retrieve hot selling products with caching and pagination .
     * @return JsonResponse
     */
    public function getBestSellingProducts()
    {
        $products = $this->ProductService->getBestSellingProducts();
        if ($products->isEmpty()) {
            return self::error(null, 'No Products matched!', 404);
        }
        return self::paginated($products, null, 'Products retrieved successfully', 200);
    }
    /**
     * Retrieve season products with caching and pagination .
     * @return JsonResponse
     */
    public function getSeasonProducts()
    {
        $products = $this->ProductService->getSeasonProducts();
        if ($products->isEmpty()) {
            return self::error(null, 'No season products Found!', 404);
        }
        return self::paginated($products, null, 'Products retrieved successfully', 200);
    }
    /**
     * Retrieve products the user may like
     * @return JsonResponse
     */
    public function getProductsUserMayLike()
    {
        $products = $this->ProductService->getProductsUserMayLike();
        if ($products->isEmpty()) {
            return self::error(null, 'Like Some Products,Please!', 404);
        }
        return self::paginated($products, null, 'Products retrieved successfully', 200);
    }
    /**
     * Retrieve top Rated Products
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function topRatedProducts(Request $request): JsonResponse
    {
        $limit = $request->get('limit', 10);
        $products = $this->ProductService->getTopRatedProducts($limit);

        return self::paginated($products, ProductResource::class, 'Top-rated products retrieved successfully', 200);
    }
    public function showLargestQuantitySold($name)
    {
        $this->authorize('largestQuantitySold', Product::class);
        $largestOrderItem = $this->ProductService->showLargestQuantitySold($name);
        return self::success($largestOrderItem, 'Largest Quantity Sold for this Product restored successfully');
    }
}
