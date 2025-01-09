<?php

namespace App\Http\Controllers\Category;

use App\Models\Category\SubCategory;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Http\Resources\SubCategoryResource;
use App\Services\Category\SubCategoryService;
use App\Http\Requests\Category\SubCategory\StoreSubCategoryRequest;
use App\Http\Requests\Category\SubCategory\UpdateSubCategoryRequest;

class SubCategoryController extends Controller
{

    protected SubCategoryService $SubCategoryService;

    public function __construct(SubCategoryService $SubCategoryService)
    {
        $this->SubCategoryService = $SubCategoryService;
    }

    /**
     * Index sub categories
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(): JsonResponse
    {
        $subCategories = $this->SubCategoryService->getSubCategorys();
        return self::paginated($subCategories, SubCategoryResource::class,'SubCategories retrieved successfully',200);
    }

    /**
     * Store a newly sub category in storage.
     * @param \App\Http\Requests\Category\SubCategory\StoreSubCategoryRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(StoreSubCategoryRequest $request): JsonResponse
    {
        $subCategory = $this->SubCategoryService->storeSubCategory($request->validated());
        return self::success(new SubCategoryResource($subCategory), 'SubCategory created successfully', 201);
    }

    /**
     * Display the specified sub category.
     * @param mixed $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(SubCategory $subCategory): JsonResponse
    {
        return self::success(new SubCategoryResource($subCategory), 'SubCategory retrieved successfully');
    }

    /**
     * Update the specified sub category in storage.
     * @param \App\Http\Requests\Category\SubCategory\UpdateSubCategoryRequest $request
     * @param \App\Models\Category\SubCategory $subCategory
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateSubCategoryRequest $request, SubCategory $subCategory): JsonResponse
    {
        $updatedSubCategory = $this->SubCategoryService->updateSubCategory($request->validated(),$subCategory);
        return self::success(new SubCategoryResource($updatedSubCategory), 'SubCategory updated successfully');
    }

    /**
     * Remove the specified sub category from storage.
     * @param mixed $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id): JsonResponse
    {
        $this->SubCategoryService->destroySubCategory($id);
        return self::success(null, 'SubCategory deleted successfully');
    }

    /**
     * Display soft-deleted records.
     * @return \Illuminate\Http\JsonResponse
     */
    public function showDeleted(): JsonResponse
    {
        $subCategorys = SubCategory::onlyTrashed()->get();
        return self::success(SubCategoryResource::collection($subCategorys), 'SubCategorys retrieved successfully');
    }

    /**
     * Restore a soft-deleted record.
     * @param string $id
     * @return JsonResponse
     */
    public function restoreDeleted($id): JsonResponse
    {
        $this->SubCategoryService->restorSubCategory($id);
        return self::success(null, 'SubCategory restored successfully');
    }

    /**
     * Permanently delete a soft-deleted record.
     * @param string $id
     * @return JsonResponse
     */
    public function forceDeleted($id): JsonResponse
    {
        $subCategory = SubCategory::onlyTrashed()->findOrFail($id)->forceDelete();
        return self::success(null, 'SubCategory force deleted successfully');
    }
}
