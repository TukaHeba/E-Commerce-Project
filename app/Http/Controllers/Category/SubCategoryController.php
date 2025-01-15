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
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(): JsonResponse
    {
        $subCategories = $this->SubCategoryService->getSubCategories();
        return self::paginated($subCategories, SubCategoryResource::class, 'SubCategories retrieved successfully', 200);
    }

    /**
     * Store a newly sub category in storage.
     *
     * @param \App\Http\Requests\Category\SubCategory\StoreSubCategoryRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(StoreSubCategoryRequest $request): JsonResponse
    {
        $this->authorize('store', SubCategory::class);
        $photos = $request->file('photos');
        $subCategory = $this->SubCategoryService->storeSubCategory($request->validated() , $photos);
        return self::success([new SubCategoryResource($subCategory['subCategory']) , 'photo' => $subCategory['photo'] ], 'SubCategory created successfully', 201);
    }

    /**
     * Display the specified sub category.
     *
     * @param mixed $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(SubCategory $subCategory): JsonResponse
    {
        return self::success(new SubCategoryResource($subCategory->load('mainCategories')), 'SubCategory retrieved successfully');
    }

    /**
     * Update the specified sub category in storage.
     *
     * @param \App\Http\Requests\Category\SubCategory\UpdateSubCategoryRequest $request
     * @param \App\Models\Category\SubCategory $subCategory
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateSubCategoryRequest $request, $id): JsonResponse
    {
        $this->authorize('update', SubCategory::class);
        $photos = $request->file('photos');
        $updatedSubCategory = $this->SubCategoryService->updateSubCategory($request->validated(), $id , $photos);
        return self::success([new SubCategoryResource($updatedSubCategory['subCategory']) , 'photo' => $updatedSubCategory['photo']], 'SubCategory updated successfully');
    }

    /**
     * Remove the specified sub category from storage.
     *
     * @param mixed $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id): JsonResponse
    {
        $this->authorize('delete', SubCategory::class);
        $this->SubCategoryService->destroySubCategory($id);
        return self::success(null, 'SubCategory deleted successfully');
    }

    /**
     * Display soft-deleted records.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function showDeleted(): JsonResponse
    {
        $this->authorize('showDeleted', SubCategory::class);
        $subCategories = SubCategory::onlyTrashed()->paginate();
        return self::paginated($subCategories, SubCategoryResource::class, 'SubCategories retrieved successfully', 200);
    }

    /**
     * Restore a soft-deleted record.
     *
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
     *
     * @param string $id
     * @return JsonResponse
     */
    public function forceDeleted($id): JsonResponse
    {
        $this->authorize('forceDeleted', SubCategory::class);
        $SubCategory = $this->SubCategoryService->forceDeleted($id);
        return self::success(null, $SubCategory);
    }
}
