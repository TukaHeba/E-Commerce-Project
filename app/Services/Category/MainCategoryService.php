<?php

namespace App\Services\Category;

use App\Traits\CacheManagerTrait;
use App\Services\Photo\PhotoService;
use App\Models\Category\MainCategory;
use Illuminate\Support\Facades\Cache;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

use Exception;


class MainCategoryService
{
    use CacheManagerTrait;
    private $groupe_key_cache = 'main_categories_cache_keys';

    protected PhotoService $photoService;

    public function __construct(PhotoService $photoService)
    {
        $this->photoService = $photoService;
    }

    /**
     * View all main categories
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function getMainCategories(): LengthAwarePaginator
    {
        $cache_key = 'main_categories';
        $this->addCacheKey($this->groupe_key_cache, $cache_key);

        return Cache::remember($cache_key, now()->addWeek(), function () {
            return MainCategory::paginate(10);
        });
    }

    /**
     * Create new main category
     * @param mixed $data
     * @return array
     */
    public function storeMainCategory($data, $photos)
    {
        try {
            DB::beginTransaction();
            $mainCategory = new MainCategory();
            $mainCategory->main_category_name = $data['main_category_name'];
            $mainCategory->save();

            if ($photos) {
                $result = $this->photoService->storeMultiplePhotos($photos, $mainCategory, 'main_category_photos');
            }

            $mainCategory->subCategories()->attach($data['sub_category_name']);
            $mainCategory->save();
            DB::commit();
            $this->clearCacheGroup($this->groupe_key_cache);
            return ['mainCategory' => $mainCategory->load('subCategories'), 'photo' => $result];
        } catch (Exception $e) {
            DB::rollBack();
            throw new Exception('Failed to create main category: ' . $e->getMessage(), $e->getCode());
        }
    }

    /**
     * update a main category and its associated sub-categories and photos
     * @param  $id
     * @return array
    */
    public function updateMainCategory(array $data, $id , $photos = null)
    {
        try {
            DB::beginTransaction();

            // Find the main category to update
            $mainCategory = MainCategory::findOrFail($id);

            // Update the main category details
            $mainCategory->main_category_name = $data['main_category_name'] ?? $mainCategory->main_category_name;
            $mainCategory->save();
            
            // Check if new photos are uploaded
            if ($photos) {
                // Delete old photos if there are new ones uploaded
                foreach ($mainCategory->photos as $photo) {
                    // Use the deletePhoto service method to delete the photo from storage and database
                    $this->photoService->deletePhoto($photo->photo_path, $photo->id);
                }

                // Store the new uploaded photos
                $photos = $data['photos'];
                $result = $this->photoService->storeMultiplePhotos($photos, $mainCategory, 'main_category_photos');
            }

            //Check for sub-categories and sync them
            if (isset($data['sub_category_name'])) {
                $mainCategory->subCategories()->sync($data['sub_category_name']);
            }
           
            // Commit the transaction
            DB::commit();

            return ['mainCategory' => $mainCategory->load('subCategories'), 'photo' => $result];
        } catch (Exception $e) {
            // Rollback in case of failure
            DB::rollBack();
            throw new Exception('Failed to update main category: ' . $e->getMessage(), $e->getCode());
        }
    }


    /**
     * method to soft delete main category alraedy exist
     * @param  $id
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
