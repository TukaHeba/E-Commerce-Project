<?php

namespace App\Http\Controllers\Category;

use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Models\Category\MainCategory;
use App\Http\Resources\MainCategoryResource;
use App\Services\Category\MainCategoryService;
use App\Http\Requests\Category\MainCategory\StoreMainCategoryRequest;
use App\Http\Requests\Category\MainCategory\UpdateMainCategoryRequest;

class MainCategoryController extends Controller
{

    protected MainCategoryService $MainCategoryService;

    public function __construct(MainCategoryService $MainCategoryService)
    {
        $this->MainCategoryService = $MainCategoryService;
    }

    /**
     * Index main categories
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(): JsonResponse
    {
        $mainCategories = $this->MainCategoryService->getMainCategories();
        return self::paginated($mainCategories, MainCategoryResource::class, 'Main categories retrieved successfully', 200);
    }

    /**
     * Store a newly main category in storage.
     * @param \App\Http\Requests\Category\MainCategory\StoreMainCategoryRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(StoreMainCategoryRequest $request): JsonResponse
    {
        $this->authorize( 'store', MainCategory::class);
        $mainCategory = $this->MainCategoryService->storeMainCategory($request->validated());
        return self::success(new MainCategoryResource($mainCategory), 'MainCategory created successfully', 201);
    }

    /**
     * Display the specified main category.
     * @param \App\Models\Category\MainCategory $mainCategory
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(MainCategory $mainCategory): JsonResponse
    {
        return self::success(new MainCategoryResource($mainCategory), 'MainCategory retrieved successfully');
    }

    /**
     * Update the specified main category in storage.
     * @throws \Exception
     */
    public function update(UpdateMainCategoryRequest $request, MainCategory $mainCategory): JsonResponse
    {
        $this->MainCategoryService->updateMainCategory($request->validated(), $mainCategory);
        return self::success(new MainCategoryResource($mainCategory), 'MainCategory updated successfully');
    }

    /**
     * Remove the specified main category from storage.
     */
    public function destroy(MainCategory $mainCategory): JsonResponse
    {
        $this->authorize('delete', MainCategory::class);
        $this->MainCategoryService->destroyMainCategory($mainCategory);
        return self::success(null, 'MainCategory deleted successfully');
    }

    /**
     * Display soft-deleted records.
     * @return \Illuminate\Http\JsonResponse
     */
    public function showDeleted(): JsonResponse
    {
        $this->authorize('showDeleted', MainCategory::class);
        $mainCategories = MainCategory::onlyTrashed()->paginate();
        return self::paginated($mainCategories, MainCategoryResource::class, 'Main categories retrieved successfully', 200);
    }

    /**
     * Restore a soft-deleted record.
     * @param string $id
     * @return JsonResponse
     */
    public function restoreDeleted($id): JsonResponse
    {
        $this->authorize('restoreDeleted',MainCategory::class);
        $restoredMainCategory = $this->MainCategoryService->restorMainCategory($id);
        return self::success(new MainCategoryResource($restoredMainCategory), 'MainCategory restored successfully');
    }

    /**
     * Permanently delete a soft-deleted record.
     * @param string $id
     * @return JsonResponse
     */
    public function forceDeleted(string $id): JsonResponse
    {
        $this->authorize('forceDeleted',MainCategory::class);
        MainCategory::onlyTrashed()->findOrFail($id)->forceDelete();
        return self::success(null, 'MainCategory force deleted successfully');
    }
}
