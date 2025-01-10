<?php

namespace App\Http\Controllers\Favorite;

use App\Models\Product\Product;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Http\Resources\UserResource;
use App\Services\Favorite\FavoriteService;

class FavoriteController extends Controller
{

    protected FavoriteService $FavoriteService;

    public function __construct(FavoriteService $FavoriteService)
    {
        $this->FavoriteService = $FavoriteService;
    }

    /*
     * Store a newly created resource in storage.
     * @throws \Exception
     */
    public function store(Product $product): JsonResponse
    {
        $this->FavoriteService->storeFavorite($product);
        return self::success(null, 'Product added to Favorite successfully', 201);
    }

    /*
     * Display the specified resource.
     */
    public function show(): JsonResponse
    {
        $user_favorite_products = $this->FavoriteService->showFavorites();
        return self::success(ProductResource::collection($user_favorite_products), 'User Favorite Products retrieved successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product): JsonResponse
    {
        $this->authorize('destroy', $product);
        $this->FavoriteService->destroyFavorite($product);
        return self::success(null, 'Product Removed from Favorite successfully');
    }
}
