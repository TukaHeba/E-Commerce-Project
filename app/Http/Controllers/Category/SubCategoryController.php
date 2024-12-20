<?php

namespace App\Http\Controllers\Category;

use App\Models\SubCategory;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Services\Category\SubCategoryService;
use App\Http\Requests\SubCategory\StoreSubCategoryRequest;
use App\Http\Requests\SubCategory\UpdateSubCategoryRequest;

class SubCategoryController extends Controller
{

    protected SubCategoryService $SubCategoryService;

    public function __construct(SubCategoryService $SubCategoryService)
    {
        $this->SubCategoryService = $SubCategoryService;
    }

    /**
     * Display a listing of the resource.
     * @throws \Exception
     */
    public function index(Request $request): JsonResponse
    {
        $subCategorys = $this->SubCategoryService->getSubCategorys($request);
        return self::paginated($subCategorys, 'SubCategorys retrieved successfully', 200);
    }

    /**
     * Store a newly created resource in storage.
     * @throws \Exception
     */
    public function store(StoreSubCategoryRequest $request): JsonResponse
    {
        $subCategory = $this->SubCategoryService->storeSubCategory($request->validated());
        return self::success($subCategory, 'SubCategory created successfully', 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(SubCategory $subCategory): JsonResponse
    {
        return self::success($subCategory, 'SubCategory retrieved successfully');
    }

    /**
     * Update the specified resource in storage.
     * @throws \Exception
     */
    public function update(UpdateSubCategoryRequest $request, SubCategory $subCategory): JsonResponse
    {
        $updatedSubCategory = $this->SubCategoryService->updateSubCategory($subCategory, $request->validated());
        return self::success($updatedSubCategory, 'SubCategory updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(SubCategory $subCategory): JsonResponse
    {
        $subCategory->delete();
        return self::success(null, 'SubCategory deleted successfully');
    }

    /**
     * Display soft-deleted records.
     */
    public function showDeleted(): JsonResponse
    {
        $subCategorys = SubCategory::onlyTrashed()->get();
        return self::success($subCategorys, 'SubCategorys retrieved successfully');
    }

    /**
     * Restore a soft-deleted record.
     * @param string $id
     * @return JsonResponse
     */
    public function restoreDeleted(string $id): JsonResponse
    {
        $subCategory = SubCategory::onlyTrashed()->findOrFail($id);
        $subCategory->restore();
        return self::success($subCategory, 'SubCategory restored successfully');
    }

    /**
     * Permanently delete a soft-deleted record.
     * @param string $id
     * @return JsonResponse
     */
    public function forceDeleted(string $id): JsonResponse
    {
        $subCategory = SubCategory::onlyTrashed()->findOrFail($id)->forceDelete();
        return self::success(null, 'SubCategory force deleted successfully');
    }
}
