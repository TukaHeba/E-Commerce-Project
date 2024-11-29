<?php

namespace App\Http\Controllers\Category;

use Illuminate\Http\Request;
use App\Models\Category\Category;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Category\StoreCategoryRequest;
use App\Http\Requests\Category\UpdateCategoryRequest;

class CategoryController extends Controller
{
  
    protected CategoryService $CategoryService;

    public function __construct(CategoryService $CategoryService)
    {
        $this->CategoryService = $CategoryService;
    }

    /**
     * Display a listing of the resource.
     * @throws \Exception
     */
    public function index(Request $request): JsonResponse
    {
        $categorys = $this->CategoryService->getCategorys($request);
        return self::paginated($categorys, 'Categorys retrieved successfully', 200);
    }

    /**
     * Store a newly created resource in storage.
     * @throws \Exception
     */
    public function store(StoreCategoryRequest $request): JsonResponse
    {
        $category = $this->CategoryService->storeCategory($request->validated());
        return self::success($category, 'Category created successfully', 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Category $category): JsonResponse
    {
        return self::success($category, 'Category retrieved successfully');
    }

    /**
     * Update the specified resource in storage.
     * @throws \Exception
     */
    public function update(UpdateCategoryRequest $request, Category $category): JsonResponse
    {
        $updatedCategory = $this->CategoryService->updateCategory($category, $request->validated());
        return self::success($updatedCategory, 'Category updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Category $category): JsonResponse
    {
        $category->delete();
        return self::success(null, 'Category deleted successfully');
    }

    /**
     * Display soft-deleted records.
     */
    public function showDeleted(): JsonResponse
    {
        $categorys = Category::onlyTrashed()->get();
        return self::success($categorys, 'Categorys retrieved successfully');
    }

    /**
     * Restore a soft-deleted record.
     * @param string $id
     * @return JsonResponse
     */
    public function restoreDeleted(string $id): JsonResponse
    {
        $category = Category::onlyTrashed()->findOrFail($id);
        $category->restore();
        return self::success($category, 'Category restored successfully');
    }

    /**
     * Permanently delete a soft-deleted record.
     * @param string $id
     * @return JsonResponse
     */
    public function forceDeleted(string $id): JsonResponse
    {
        $category = Category::onlyTrashed()->findOrFail($id)->forceDelete();
        return self::success(null, 'Category force deleted successfully');
    }
}
