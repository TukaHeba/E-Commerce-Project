<?php

namespace App\Http\Controllers\Favorite;

use App\Models\User\User;
use App\Models\Product\Product;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Http\Resources\ProductResource;
use App\Services\Favorite\FavoriteService;

use function PHPUnit\Framework\isEmpty;

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
        if (!auth()->check()) {
            return self::error(null, "User not authenticated", 401);
        }

        $result = $this->FavoriteService->storeFavorite($product, auth()->user()->id);

        if ($result) {
            return self::success(null, 'Product added to Favorite successfully', 201);
        }
        return self::error(null, "Product is already in favorites", 409);
    }

    /*
     * Display the specified resource.
     */
    public function show(): JsonResponse
    {
        $user_favorite_products = $this->FavoriteService->showFavorites();
        if(isEmpty($user_favorite_products)){
            return self::error(null, "You do not have favorite  products,Add some product to favorite.",404);
        }
        return self::success(ProductResource::collection($user_favorite_products), 'User Favorite Products retrieved successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product): JsonResponse
    {
        $userId = auth()->user()->id;
        $result = $this->FavoriteService->destroyFavorite($product , $userId);
        if ($result) {
            return self::success(null, 'Product Removed from Favorite successfully');
        }
        return self::error(null, "You do not have permission to access this resource.", 403);
    }
}
