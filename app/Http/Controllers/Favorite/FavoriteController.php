<?php

namespace App\Http\Controllers\Favorite;

use App\Models\User\User;
use Illuminate\Http\Request;
use App\Models\Product\Product;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Http\Resources\ProductResource;
use App\Services\Favorite\FavoriteService;
use App\Http\Requests\Favorite\StoreFavoriteRequest;

class FavoriteController extends Controller
{

    protected FavoriteService $FavoriteService;

    public function __construct(FavoriteService $FavoriteService)
    {
        $this->FavoriteService = $FavoriteService;
    }

    /**
     * Display a listing of the resource.
     * @throws \Exception
     */
    public function index(): JsonResponse
    {
        $favorites = $this->FavoriteService->getFavorites();
        return self::paginated($favorites, UserResource::class, 'Favorites retrieved successfully', 200);
    }

    /**
     * Store a newly created resource in storage.
     * @throws \Exception
     */
    public function store(StoreFavoriteRequest $request): JsonResponse
    {
        $this->FavoriteService->storeFavorite($request->validated());
        return self::success(null, 'Favorite created successfully', 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user): JsonResponse
    {
        $user_favorite_products = $this->FavoriteService->showFavorites($user);
        return self::success(new UserResource($user_favorite_products), 'User Favorite Products retrieved successfully');
    }
    /**
     * Display the specified resource.
     */
    public function usersFavoringProduct(Product $product): JsonResponse
    {
        $users_favoring_product = $this->FavoriteService->usersFavoringProduct($product);
        return self::success(new ProductResource($users_favoring_product), 'Users who favorited this product retrieved successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request): JsonResponse
    {
        $this->FavoriteService->destroyFavorite($request);
        return self::success(null, 'Favorite deleted successfully');
    }
}
