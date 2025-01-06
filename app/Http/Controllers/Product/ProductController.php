<?php

namespace App\Http\Controllers\Product;

use Illuminate\Http\Request;
use App\Models\Product\Product;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Services\Photo\PhotoService;
use App\Services\Product\ProductService;
use App\Http\Requests\Product\StoreProductRequest;
use App\Http\Requests\Product\UpdateProductRequest;
use App\Http\Requests\Photo\StoreMultiplePhotosRequest;

class ProductController extends Controller
{
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
        return self::paginated($products, null, 'Products retrieved successfully', 200);
    }

    /**
     * Display details of a specific product.
     *
     * @param \App\Models\Product\Product $product
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Product $product): JsonResponse
    {
        return self::success($product, 'Product retrieved successfully');
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
        $photos = $storeMultiplePhotosRequest->file('photos');
        $product = $this->ProductService->storeProduct($request->validated(), $photos);
        return self::success($product, 'Product created successfully', 201);
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
        $deletedPhotos = $request->input('photosDeleted');
        $updatedProduct = $this->ProductService->updateProduct($product, $request->validated(), $deletedPhotos);
        return self::success($updatedProduct, 'Product updated successfully');
    }

    /**
     * Remove a product from the database (soft delete).
     *
     * @param \App\Models\Product\Product $product
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Product $product): JsonResponse
    {
        $product->delete();
        $this->ProductService->clearProductCache();
        return self::success(null, 'Product deleted successfully');
    }

    /**
     * Retrieve all soft-deleted products.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function showDeleted(): JsonResponse
    {
        $products = Product::onlyTrashed()->get();
        return self::success($products, 'Products retrieved successfully');
    }

    /**
     * Restore a soft-deleted product.
     *
     * @param string $id The ID of the product to restore.
     * @return \Illuminate\Http\JsonResponse
     */
    public function restoreDeleted(string $id): JsonResponse
    {
        $product = Product::onlyTrashed()->findOrFail($id);
        $product->restore();
        $this->ProductService->clearProductCache();
        return self::success($product, 'Product restored successfully');
    }

    /**
     * Permanently delete a soft-deleted product.
     *
     * @param string $id The ID of the product to permanently delete.
     * @return \Illuminate\Http\JsonResponse
     */
    public function forceDeleted(string $id): JsonResponse
    {
        Product::onlyTrashed()->findOrFail($id)->forceDelete();
        $this->ProductService->clearProductCache();
        return self::success(null, 'Product force deleted successfully');
    }
}
