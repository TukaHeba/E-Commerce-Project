<?php

namespace App\Services\Category;

use App\Traits\CacheManagerTrait;
use App\Models\Category\MainCategory;
use Illuminate\Support\Facades\Cache;
use Illuminate\Pagination\LengthAwarePaginator;

class MainCategoryService
{
    use CacheManagerTrait;
    private $groupe_key_cache = 'main_categories_cache_keys';
    /**
     * View all main categories
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function getMainCategories(): LengthAwarePaginator
    {
        $cache_key = 'main_categories';
        $this->addCacheKey($this->groupe_key_cache, $cache_key);

        return Cache::remember($cache_key, now()->addWeek(), function () {
            return MainCategory::with('subCategories')->paginate(10);
        });
    }

    /**
     * Create new main category
     * @param mixed $data
     * @return MainCategory
     */
    public function storeMainCategory($data)
    {

        $mainCategory = new MainCategory();
        $mainCategory->main_category_name = $data['main_category_name'];
        $mainCategory->save();

        $mainCategory->subCategories()->attach($data['sub_category_name']);
        $mainCategory->save();

        $this->clearCacheGroup($this->groupe_key_cache);
        return $mainCategory;
    }

    /**
     * method to update main category alraedy exist
     * @param   $data
     * @param   $id
     * @return /Illuminate\Http\JsonResponse if have an error
     */

    public function updateMainCategory($data, $id)
    {
        $maincategory = MainCategory::findOrFail($id);
        $maincategory->main_category_name = $data['main_category_name'] ?? $maincategory->main_category_name;
        $maincategory->save();

        if ($data['sub_category_name'] != null) {
            $maincategory->subCategories()->sync($data['sub_category_name']);
            $maincategory->save();
        }
        
        $this->clearCacheGroup($this->groupe_key_cache);
        return $maincategory;
    }

    /**
     * method to soft delete main category alraedy exist
     * @param  $mainCategory
     * @return /Illuminate\Http\JsonResponse if have an error
     */
    public function destroyMainCategory($id)
    {

        $maincategory = MainCategory::findOrFail($id);
        $maincategory->delete();
        $maincategory->subCategories()->updateExistingPivot($maincategory->subCategories->pluck('id'), ['deleted_at' => now()]);

        $this->clearCacheGroup($this->groupe_key_cache);
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
        $this->clearCacheGroup($this->groupe_key_cache);
        return true;
    }
}
