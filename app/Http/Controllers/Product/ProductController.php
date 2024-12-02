<?php

namespace App\Http\Controllers\Product;

use Illuminate\Http\Request;
use App\Models\Product\Product;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Product\StoreProductRequest;
use App\Http\Requests\Product\UpdateProductRequest;
use App\Services\Product\ProductService;

class ProductController extends Controller
{

    protected ProductService $ProductService;

    public function __construct(ProductService $ProductService)
    {
        $this->ProductService = $ProductService;
    }

    /**
     * Display a listing of the resource.
     * @throws \Exception
     */
    public function index(Request $request): JsonResponse
    {
        $products = $this->ProductService->getProducts($request);
        return self::success($products, 'Products retrieved successfully', 200);
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
     * Display a listing of the Products With spicification Filter
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getProductsWithFilter(Request $request)
    {
        $products = $this->ProductService->getProductsWithFilter($request);

        if ($products->isEmpty()) {
            return response()->json(['message' => 'No Products matched!'], 404);
        }
        return self::success($products, 'Products retrieved successfully', 200);
    }

    /**
     *  Display a listing of the Products filtered By Category
     * @param mixed $categoryID
     * @return mixed
     */
    public function getProductsByCategory($categoryID)
    {
        $products = $this->ProductService->getProductsByCategory($categoryID);
        if ($products->isEmpty()) {
            return response()->json(['message' => 'No Products matched!'], 404);
        }
        return self::success($products, 'Products retrieved successfully', 200);
    }

    /**
     * Display a listing of the latest Products
     * @return mixed
     */
    public function getLatestProducts()
    {
        $products = $this->ProductService->getLatestProducts();

        // Check if the paginated collection is empty.
        if ($products->isEmpty()) {
            return response()->json(['message' => 'No products found!'], 404);
        }

        // Return paginated products with a success message.
        return self::success($products, 'Products retrieved successfully', 200);
    }
}
