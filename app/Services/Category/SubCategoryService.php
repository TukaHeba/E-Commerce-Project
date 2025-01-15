<?php

namespace App\Services\Category;

use Exception;
use App\Traits\CacheManagerTrait;
use Illuminate\Support\Facades\DB;
use App\Models\Category\SubCategory;
use App\Services\Photo\PhotoService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Pagination\LengthAwarePaginator;

class SubCategoryService
{
    use CacheManagerTrait;
    private $groupe_key_cache = 'sub_categories_cache_keys';

    protected PhotoService $photoService;

    public function __construct(PhotoService $photoService)
    {
        $this->photoService = $photoService;
    }
    /**
     * View all sub categories
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function getSubCategories(): LengthAwarePaginator
    {
        $cache_key = 'sub_categories';
        $this->addCacheKey($this->groupe_key_cache, $cache_key);

        return Cache::remember($cache_key, now()->addWeek(), function (): LengthAwarePaginator {
            return SubCategory::paginate(10);
        });
    }

    /**
     * Create new sub category
     * @param mixed $data
     * @return array
     */
    public function storeSubCategory($data, $photos = null)
    {
        try {
            DB::beginTransaction();
            $subcategory = new SubCategory();
            $subcategory->sub_category_name = $data['sub_category_name'];
            $subcategory->save();

            if ($photos) {
                $result = $this->photoService->storeMultiplePhotos($photos, $subcategory, 'sub_category_photos');
            }

            $subcategory->mainCategories()->attach($data['main_category_name']);
            $subcategory->save();
            DB::commit();

            $this->clearCacheGroup($this->groupe_key_cache);
            return ['subCategory' => $subcategory->load('mainCategories'), 'photo' => $result];
        } catch (Exception $e) {
            DB::rollBack();
            throw new Exception('Failed to create subCateory : ' . $e->getMessage(), $e->getCode());
        }
    }

    /**
     * Update sub category alraedy exist
     * @param   $data
     * @param   $id
     * @return array
     */
    public function updateSubCategory($data, $id, $photos)
    {
        try {
            DB::beginTransaction();
            //find the subcategory to update 
            $subCategory = SubCategory::findOrFail($id);

            //update the subcategory details
            $subCategory->sub_category_name = $data['sub_category_name'] ?? $subCategory->sub_category_name;
            $subCategory->save();

            // Check if new photos are uploaded
            if ($photos) {
                // Delete old photos if there are new ones uploaded
                foreach ($subCategory->photos as $photo) {
                    if ($photo) {
        
                        // Use the deletePhoto service method to delete the photo from storage and database
                        $this->photoService->deletePhoto($photo->photo_path, $photo->id);
                    }
                }

                // Store the new uploaded photos
                $result = $this->photoService->storeMultiplePhotos($photos, $subCategory, 'sub_category_photos');
            }

             //Check for sub-categories and sync them
             if (isset($data['main_category_name'])) {
                $subCategory->mainCategories()->sync($data['main_category_name']);
            }

            // Commit the transaction
            DB::commit();

            $this->clearCacheGroup($this->groupe_key_cache);
            return ['subCategory' => $subCategory->load('mainCategories'), 'photo' => $result];
        } catch (Exception $e) {
            // Rollback in case of failure
            DB::rollBack();
            throw new Exception('Failed to update sub category: ' . $e->getMessage(), $e->getCode());
        }
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


    /**
     * Method to force delete a subcategory and its associated main category photos (with try-catch for error handling).
     * 
     * @param  int  $id
     * @return string
     */
    public function forceDeleted($id)
    {
        try {
            $subCategory = SubCategory::withTrashed()->findOrFail($id);

            // Get all photos associated with the subcategory
            $subCategoryPhotos = $subCategory->photos;

            // Delete subcategory photo files from storage
            foreach ($subCategoryPhotos as $photo) {
                if ($photo) {
                    $this->photoService->deletePhoto($photo->photo_path, $photo->id);
                }
            }

            // delete subcategory photos in the database
            $subCategory->photos()->delete();

            // Remove pivot table records
            $subCategory->mainCategories()->detach();

            // Force delete the subcategory
            $subCategory->forceDelete();

            // Clear related cache if applicable
            $this->clearCacheGroup($this->groupe_key_cache);

            return 'MainCategory force deleted successfully';
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred while force deleting the subcategory.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
