<?php

namespace App\Http\Controllers\Product;

use Illuminate\Http\Request;
use App\Models\Product\Product;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Product\StoreProductRequest;
use App\Http\Requests\Product\UpdateProductRequest;
use App\Http\Resources\OrderItemResource;
use App\Http\Resources\ProductResource;
use App\Services\Product\ProductService;

class ProductController extends Controller
{

    protected ProductService $ProductService;

    public function __construct(ProductService $ProductService)
    {
        $this->ProductService = $ProductService;
    }

    /**
     * Store a newly created resource in storage.
     * @throws \Exception
     */
    public function store(StoreProductRequest $request): JsonResponse
    {
        $product = $this->ProductService->storeProduct($request->validated());
        return self::success($product, 'Product created successfully', 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product): JsonResponse
    {
        return self::success($product, 'Product retrieved successfully');
    }

    /**
     * Update the specified resource in storage.
     * @throws \Exception
     */
    public function update(UpdateProductRequest $request, Product $product): JsonResponse
    {
        $updatedProduct = $this->ProductService->updateProduct($product, $request->validated());
        return self::success($updatedProduct, 'Product updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product): JsonResponse
    {
        $product->delete();
        return self::success(null, 'Product deleted successfully');
    }

    /**
     * Display soft-deleted records.
     */
    public function showDeleted(): JsonResponse
    {
        $products = Product::onlyTrashed()->get();
        return self::success($products, 'Products retrieved successfully');
    }

    /**
     * Restore a soft-deleted record.
     * @param string $id
     * @return JsonResponse
     */
    public function restoreDeleted(string $id): JsonResponse
    {
        $product = Product::onlyTrashed()->findOrFail($id);
        $product->restore();
        return self::success($product, 'Product restored successfully');
    }

    /**
     * Permanently delete a soft-deleted record.
     * @param string $id
     * @return JsonResponse
     */
    public function forceDeleted(string $id): JsonResponse
    {
        $product = Product::onlyTrashed()->findOrFail($id)->forceDelete();
        return self::success(null, 'Product force deleted successfully');
    }
    /**
     * Display a listing of the Products With spicification Filter  //index//
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $products = $this->ProductService->getProductsWithFilter($request);

        if ($products->isEmpty()) {
            return self::error(null, 'No Products matched!', 404);
        }
        return self::paginated($products, null, 'Products retrieved successfully', 200);
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
        $largestOrderItem = $this->ProductService->showLargestQuantitySold($name);
        return self::success($largestOrderItem, 'Largest Quantity Sold for this Product restored successfully');
    }


}
