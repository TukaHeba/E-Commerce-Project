<?php

namespace App\Http\Controllers\Category;

use App\Models\MainCategory;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Services\Category\MainCategoryService;
use App\Http\Requests\MainCategory\StoreMainCategoryRequest;
use App\Http\Requests\MainCategory\UpdateMainCategoryRequest;

class MainCategoryController extends Controller
{

    protected MainCategoryService $MainCategoryService;

    public function __construct(MainCategoryService $MainCategoryService)
    {
        $this->MainCategoryService = $MainCategoryService;
    }

    /**
     * Display a listing of the resource.
     * @throws \Exception
     */
    public function index(Request $request): JsonResponse
    {
        $mainCategorys = $this->MainCategoryService->getMainCategorys($request);
        return self::paginated($mainCategorys, 'MainCategorys retrieved successfully', 200);
    }

    /**
     * Store a newly created resource in storage.
     * @throws \Exception
     */
    public function store(StoreMainCategoryRequest $request): JsonResponse
    {
        $mainCategory = $this->MainCategoryService->storeMainCategory($request->validated());
        return self::success($mainCategory, 'MainCategory created successfully', 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(MainCategory $mainCategory): JsonResponse
    {
        return self::success($mainCategory, 'MainCategory retrieved successfully');
    }

    /**
     * Update the specified resource in storage.
     * @throws \Exception
     */
    public function update(UpdateMainCategoryRequest $request, MainCategory $mainCategory): JsonResponse
    {
        $updatedMainCategory = $this->MainCategoryService->updateMainCategory($mainCategory, $request->validated());
        return self::success($updatedMainCategory, 'MainCategory updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(MainCategory $mainCategory): JsonResponse
    {
        $mainCategory->delete();
        return self::success(null, 'MainCategory deleted successfully');
    }

    /**
     * Display soft-deleted records.
     */
    public function showDeleted(): JsonResponse
    {
        $mainCategorys = MainCategory::onlyTrashed()->get();
        return self::success($mainCategorys, 'MainCategorys retrieved successfully');
    }

    /**
     * Restore a soft-deleted record.
     * @param string $id
     * @return JsonResponse
     */
    public function restoreDeleted(string $id): JsonResponse
    {
        $mainCategory = MainCategory::onlyTrashed()->findOrFail($id);
        $mainCategory->restore();
        return self::success($mainCategory, 'MainCategory restored successfully');
    }

    /**
     * Permanently delete a soft-deleted record.
     * @param string $id
     * @return JsonResponse
     */
    public function forceDeleted(string $id): JsonResponse
    {
        $mainCategory = MainCategory::onlyTrashed()->findOrFail($id)->forceDelete();
        return self::success(null, 'MainCategory force deleted successfully');
    }
}
