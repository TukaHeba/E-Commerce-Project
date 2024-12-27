<?php

namespace App\Services\Category;

use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Jobs\SendNotification;
use App\Models\Category\MainCategory;

class MainCategoryService
{
    /**
     * method to view all main categories with a filtes on (????)
     * @return /Illuminate\Http\JsonResponse if have an error
     */
    public function getMainCategorys($request)
    {

        $maincategories = MainCategory::with('subCategories')->get();
        return $maincategories;
    }

    /**
     * method to creta new main category
     * @param   $data
     * @return /Illuminate\Http\JsonResponse if have an error
     */
    public function storeMainCategory($data)
    {

        $maincategory = new MainCategory();
        $maincategory->main_category_name = $data['main_category_name'];
        $maincategory->save();

        $maincategory->subCategories()->attach($data['sub_category_name']);
        $maincategory->save();

        $name = $maincategory->main_category_name;
        SendNotification::dispatch($name);

        return $maincategory;
    }

    /**
     * method to update main category alraedy exist
     * @param   $data
     * @param   MainCategory $maincategory
     * @return /Illuminate\Http\JsonResponse if have an error
     */
    public function updateMainCategory($data, MainCategory $maincategory)
    {
        $maincategory->main_category_name = $data['main_category_name'] ?? $maincategory->main_category_name;
        $maincategory->save();

        if ($data['sub_category_name'] != null) {
            $maincategory->subCategories()->sync($data['sub_category_name']);
            $maincategory->save();
        }

        return $maincategory;
    }

    /**
     * method to soft delete main category alraedy exist
     * @param  $id
     * @return /Illuminate\Http\JsonResponse if have an error
     */
    public function destroyMainCategory($id)
    {
        $mainCategory = MainCategory::findOrFail($id);
        $mainCategory->delete();
        $mainCategory->subCategories()->updateExistingPivot($mainCategory->subCategories->pluck('id'), ['deleted_at' => now()]);
        return true;
    }
    /**
     * method to soft delete main category alraedy exist
     * @param  $id
     * @return /Illuminate\Http\JsonResponse if have an error
     */
    public function restorMainCategory($id)
    {
        $mainCategory = MainCategory::onlyTrashed()->findOrFail($id);
        $mainCategory->subCategories()->withTrashed()->updateExistingPivot($mainCategory->subCategories->pluck('id'), ['deleted_at' => null]);
        $mainCategory->restore();
        return true;
    }
}
