<?php
namespace App\Services\Category;

use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Models\Category\MainCategory;

class MainCategoryService{
    /**
     * method to view all main categories with a filtes on (????)
     * @return /Illuminate\Http\JsonResponse if have an error
     */
    public function getMainCategorys($request){
        try {
            $maincategories = MainCategory::with('subCategories')->get();
            return $maincategories;
        } catch (\Throwable $th) { Log::error($th->getMessage()); return Controller::error(null ,'Something went wrong with fetche main categories');}
    }
//========================================================================================================================
    /**
     * method to creta new main category
     * @param   $data
     * @return /Illuminate\Http\JsonResponse if have an error
     */
    public function storeMainCategory($data){
        try {
            $maincategory = new MainCategory();
            $maincategory->main_category_name = $data['main_category_name'];
            $maincategory->save();

            $maincategory->subCategories()->attach($data['sub_category_name']);
            $maincategory->save();

            return $maincategory;
        } catch (\Throwable $th) { Log::error($th->getMessage()); return Controller::error(null ,'Something went wrong with create new main category');}
    }
//========================================================================================================================
    /**
     * method to update main category alraedy exist
     * @param   $data
     * @param   MainCategory $maincategory
     * @return /Illuminate\Http\JsonResponse if have an error
     */
    public function updateMainCategory($data,MainCategory $maincategory){
        try {
            $maincategory->main_category_name = $data['main_category_name'] ?? $maincategory->main_category_name;
            $maincategory->save();

            $maincategory->subCategories()->sync($data['sub_category_name'] ?? []);
            $maincategory->save();

            return $maincategory;
        } catch (\Throwable $th) { Log::error($th->getMessage()); return Controller::error(null ,'Something went wrong with update new main category');}
    }
//========================================================================================================================  
    /**
     * method to soft delete main category alraedy exist
     * @param  $id
     * @return /Illuminate\Http\JsonResponse if have an error
     */
    public function destroyMainCategory($id)
    {
        try {  
            $mainCategory = MainCategory::findOrFail($id);
            $mainCategory->delete();
            $mainCategory->subCategories()->updateExistingPivot($mainCategory->subCategories->pluck('id'), ['deleted_at' => now()]); 
            return true;
        } catch (\Throwable $th) { Log::error($th->getMessage()); return Controller::error(null ,'Something went wrong with soft delete main category');}
    }
//========================================================================================================================
    /**
     * method to soft delete main category alraedy exist
     * @param  $id
     * @return /Illuminate\Http\JsonResponse if have an error
     */
    public function restorMainCategory($id)
    {
        try {  
            $mainCategory = MainCategory::onlyTrashed()->findOrFail($id);
            $mainCategory->subCategories()->withTrashed()->updateExistingPivot($mainCategory->subCategories->pluck('id'), ['deleted_at' => null]);
            $mainCategory->restore();
            return true;
        } catch (\Throwable $th) { Log::error($th->getMessage()); return Controller::error(null ,'Something went wrong with restor main category');}
    }
    //========================================================================================================================
}
