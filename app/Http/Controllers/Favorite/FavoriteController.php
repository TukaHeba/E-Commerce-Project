<?php

namespace App\Http\Controllers\Favorite;

use App\Http\Requests\Favorite\StoreFavoriteRequest;
use App\Http\Requests\Favorite\UpdateFavoriteRequest;
use App\Models\Favorite\Favorite;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;


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
    public function index(Request $request): JsonResponse
    {
        $favorites = $this->FavoriteService->getFavorites($request);
        return self::paginated($favorites, 'Favorites retrieved successfully', 200);
    }

    /**
     * Store a newly created resource in storage.
     * @throws \Exception
     */
    public function store(StoreFavoriteRequest $request): JsonResponse
    {
        $favorite = $this->FavoriteService->storeFavorite($request->validated());
        return self::success($favorite, 'Favorite created successfully', 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Favorite $favorite): JsonResponse
    {
        return self::success($favorite, 'Favorite retrieved successfully');
    }

    /**
     * Update the specified resource in storage.
     * @throws \Exception
     */
    public function update(UpdateFavoriteRequest $request, Favorite $favorite): JsonResponse
    {
        $updatedFavorite = $this->FavoriteService->updateFavorite($favorite, $request->validated());
        return self::success($updatedFavorite, 'Favorite updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Favorite $favorite): JsonResponse
    {
        $favorite->delete();
        return self::success(null, 'Favorite deleted successfully');
    }

    /**
     * Display soft-deleted records.
     */
    public function showDeleted(): JsonResponse
    {
        $favorites = Favorite::onlyTrashed()->get();
        return self::success($favorites, 'Favorites retrieved successfully');
    }

    /**
     * Restore a soft-deleted record.
     * @param string $id
     * @return JsonResponse
     */
    public function restoreDeleted(string $id): JsonResponse
    {
        $favorite = Favorite::onlyTrashed()->findOrFail($id);
        $favorite->restore();
        return self::success($favorite, 'Favorite restored successfully');
    }

    /**
     * Permanently delete a soft-deleted record.
     * @param string $id
     * @return JsonResponse
     */
    public function forceDeleted(string $id): JsonResponse
    {
        $favorite = Favorite::onlyTrashed()->findOrFail($id)->forceDelete();
        return self::success(null, 'Favorite force deleted successfully');
    }
}
