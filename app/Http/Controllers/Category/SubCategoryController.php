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
     * Display a listing of the resource.
     * @throws \Exception
     */
    public function index(Request $request): JsonResponse
    {
        $subCategories = $this->SubCategoryService->getSubCategorys($request);
        return self::success(SubCategoryResource::collection($subCategories), 'SubCategory retrieved successfully', 200);
    }

    /**
     * Store a newly created resource in storage.
     * @throws \Exception
     */
    public function store(StoreSubCategoryRequest $request): JsonResponse
    {
        $this->authorize('store', SubCategory::class);
        $subCategory = $this->SubCategoryService->storeSubCategory($request->validated());
        return self::success(new SubCategoryResource($subCategory), 'SubCategory created successfully', 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id): JsonResponse
    {
        $subCategory = SubCategory::findOrFail($id);
        return self::success(new SubCategoryResource($subCategory), 'SubCategory retrieved successfully');
    }

    /**
     * Update the specified resource in storage.
     * @throws \Exception
     */
    public function update(UpdateSubCategoryRequest $request, $id): JsonResponse
    {
        $this->authorize('update', SubCategory::class);
        $updatedSubCategory = $this->SubCategoryService->updateSubCategory($request->validated(),$id);
        return self::success(new SubCategoryResource($updatedSubCategory), 'SubCategory updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id): JsonResponse
    {
        $this->authorize('delete', SubCategory::class);
        $this->SubCategoryService->destroySubCategory($id);    
        return self::success(null, 'SubCategory deleted successfully');
    }

    /**
     * Display soft-deleted records.
     */
    public function showDeleted(): JsonResponse
    {
        $this->authorize('showDeleted', SubCategory::class);
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
        $this->authorize('restoreDeleted', SubCategory::class);
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
        $this->authorize('forceDeleted', SubCategory::class);
        $subCategory = SubCategory::onlyTrashed()->findOrFail($id)->forceDelete();
        return self::success(null, 'SubCategory force deleted successfully');
    }
}
