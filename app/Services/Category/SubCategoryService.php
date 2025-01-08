<?php

namespace App\Services\Category;

use App\Models\Category\SubCategory;

class SubCategoryService
{
    /**
     * method to view all sub categories with a filtes on (????)
     * @return /Illuminate\Http\JsonResponse if have an error
     */
    public function getSubCategorys($request)
    {
        $subcategories = SubCategory::with('mainCategories')->get();
        return $subcategories;
    }

    /**
     * method to creta new sub category
     * @param   $data
     * @return /Illuminate\Http\JsonResponse if have an error
     */
    public function storeSubCategory($data)
    {
        $subcategory = new SubCategory();
        $subcategory->sub_category_name = $data['sub_category_name'];
        $subcategory->save();

        $subcategory->mainCategories()->attach($data['main_category_name']);
        $subcategory->save();

        return $subcategory;
    }
    /**
     * method to update sub category alraedy exist
     * @param   $data
     * @param   SubCategory $subcategory
     * @return /Illuminate\Http\JsonResponse if have an error
     */
    public function updateSubCategory($data, $id)
    {
        $subcategory = SubCategory::findOrFail($id);
        $subcategory->sub_category_name = $data['sub_category_name'] ?? $subcategory->sub_category_name;
        $subcategory->save();

        if ($data['main_category_name'] != null) {
            $subcategory->mainCategories()->sync($data['main_category_name']);
            $subcategory->save();
        }

        return $subcategory;
    }
    /**
     * method to soft delete sub category alraedy exist
     * @param  $id
     * @return /Illuminate\Http\JsonResponse if have an error
     */
    public function destroySubCategory($id)
    {
        $subCategory = SubCategory::findOrFail($id);
        $subCategory->delete();
        $subCategory->mainCategories()->updateExistingPivot($subCategory->mainCategories->pluck('id'), ['deleted_at' => now()]);
        return true;
    }
    /**
     * method to soft delete sub category alraedy exist
     * @param  $id
     * @return /Illuminate\Http\JsonResponse if have an error
     */
    public function restorSubCategory($id)
    {
        $subCategory = SubCategory::onlyTrashed()->findOrFail($id);
        $subCategory->mainCategories()->withTrashed()->updateExistingPivot($subCategory->mainCategories->pluck('id'), ['deleted_at' => null]);
        $subCategory->restore();
        return true;
    }
}
