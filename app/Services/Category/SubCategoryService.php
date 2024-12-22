<?php
namespace App\Services\Category;

use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Models\Category\SubCategory;

class SubCategoryService{
    /**
     * method to view all sub categories with a filtes on (????)
     * @return /Illuminate\Http\JsonResponse if have an error
     */
    public function getSubCategorys($request){
        try {
            $subcategories = SubCategory::with('mainCategories')->get();
            return $subcategories;
        } catch (\Throwable $th) { Log::error($th->getMessage()); return Controller::error(null ,'Something went wrong with fetche sub categories');}
    }
//========================================================================================================================
    /**
     * method to creta new sub category
     * @param   $data
     * @return /Illuminate\Http\JsonResponse if have an error
     */
    public function storeSubCategory($data){
        try {
            $subcategory = new SubCategory();
            $subcategory->sub_category_name = $data['sub_category_name'];
            $subcategory->save();

            $subcategory->mainCategories()->attach($data['main_category_name']);
            $subcategory->save();

            return $subcategory;
        } catch (\Throwable $th) { Log::error($th->getMessage()); return Controller::error(null ,'Something went wrong with create new sub category');}
    }
//========================================================================================================================
    /**
     * method to update sub category alraedy exist
     * @param   $data
     * @param   SubCategory $subcategory
     * @return /Illuminate\Http\JsonResponse if have an error
     */
    public function updateSubCategory($data,$id){
        try {
            $subcategory = SubCategory::findOrFail($id);
            $subcategory->sub_category_name = $data['sub_category_name'] ?? $subcategory->sub_category_name;
            $subcategory->save();

            if($data['main_category_name'] != null){
                $subcategory->mainCategories()->sync($data['main_category_name']);
                $subcategory->save();
            }

            return $subcategory;
        } catch (\Throwable $th) { Log::error($th->getMessage()); return Controller::error(null ,'Something went wrong with update new sub category');}
    }
//========================================================================================================================  
    /**
     * method to soft delete sub category alraedy exist
     * @param  $id
     * @return /Illuminate\Http\JsonResponse if have an error
     */
    public function destroySubCategory($id)
    {
        try {  
            $subCategory = SubCategory::findOrFail($id);
            $subCategory->delete();
            $subCategory->mainCategories()->updateExistingPivot($subCategory->mainCategories->pluck('id'), ['deleted_at' => now()]); 
            return true;
        } catch (\Throwable $th) { Log::error($th->getMessage()); return Controller::error(null ,'Something went wrong with soft delete sub category');}
    }
//========================================================================================================================
    /**
     * method to soft delete sub category alraedy exist
     * @param  $id
     * @return /Illuminate\Http\JsonResponse if have an error
     */
    public function restorSubCategory($id)
    {
        try {  
            $subCategory = SubCategory::onlyTrashed()->findOrFail($id);
            $subCategory->mainCategories()->withTrashed()->updateExistingPivot($subCategory->mainCategories->pluck('id'), ['deleted_at' => null]);
            $subCategory->restore();
            return true;
        } catch (\Throwable $th) { Log::error($th->getMessage()); return Controller::error(null ,'Something went wrong with restor sub category');}
    }
    //========================================================================================================================.
}
