<?php

namespace App\Services\Category;

use App\Traits\CacheManagerTrait;
use App\Models\Category\SubCategory;
use Illuminate\Support\Facades\Cache;
use Illuminate\Pagination\LengthAwarePaginator;

class SubCategoryService
{
    use CacheManagerTrait;
    private $groupe_key_cache = 'sub_categories_cache_keys';

    /**
     * View all sub categories
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function getSubCategories(): LengthAwarePaginator
    {
        $cache_key = 'sub_categories';
        $this->addCacheKey($this->groupe_key_cache, $cache_key);

        return Cache::remember($cache_key, now()->addWeek(), function (): LengthAwarePaginator {
              return SubCategory::with('mainCategories')->paginate(10);
        });
    }

    /**
     * Create new sub category
     * @param mixed $data
     * @return SubCategory
     */
    public function storeSubCategory($data)
    {
        $subcategory = new SubCategory();
        $subcategory->sub_category_name = $data['sub_category_name'];
        $subcategory->save();

        $subcategory->mainCategories()->attach($data['main_category_name']);
        $subcategory->save();

        $this->clearCacheGroup($this->groupe_key_cache);
        return $subcategory;
    }
    /**
     * Update sub category alraedy exist
     * @param   $data
     * @param   $id
     * @return /Illuminate\Http\JsonResponse if have an error
     */
    public function updateSubCategory($data, $id)
    {
        $subCategory = SubCategory::findOrFail($id);
        $subCategory->sub_category_name = $data['sub_category_name'] ?? $subCategory->sub_category_name;
        $subCategory->save();

        if ($data['main_category_name'] != null) {
            $subCategory->mainCategories()->sync($data['main_category_name']);
            $subCategory->save();
        }
        $this->clearCacheGroup($this->groupe_key_cache);
        return $subCategory;
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
        $this->clearCacheGroup($this->groupe_key_cache);
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
        $this->clearCacheGroup($this->groupe_key_cache);
        return true;
    }
}
