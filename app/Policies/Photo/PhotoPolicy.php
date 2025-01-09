<?php
namespace App\Policies\Photo;

use App\Models\User\User;
use App\Models\Photo\Photo;
use App\Models\Product\Product;
use App\Models\Category\SubCategory;
use App\Models\Category\MainCategory;
use Illuminate\Auth\Access\HandlesAuthorization;

class PhotoPolicy
{
    use HandlesAuthorization;

    /**
     * Determine if the user can upload a photo.
     */
    public function create(User $user, $model): bool
    {
        // If the model is User, allow only the user with the account
        if ($model instanceof User) {
            return $user->id === $model->id;
        }

        // If the model is Product, MainCategory or SubCategory, allow admin and store manager
        if ($model instanceof Product || $model instanceof MainCategory || $model instanceof SubCategory) {
            return $user->hasRole(['admin', 'store manager']);
        }

        return false; // Deny by default
    }

    /**
     * Determine if the user can delete a photo.
     */
    public function delete(User $user, Photo $photo): bool
    {
        // Check if the photo is attached to a user model (only owner can delete)
        if ($photo->photoable_type === 'User') {
            return $user->id === $photo->photoable_id; // Only the user can delete their own photo
        }

        // If the photo is attached to a product, main-category or sub-category, only admin can delete
        if ($photo->photoable_type === 'Product' || $photo->photoable_type === 'MainCategory' || $photo->photoable_type === 'SubCategory') {
            return $user->hasRole('admin'); // Only admin can delete photos from these models
        }

        return false; // Deny by default
    }
}
