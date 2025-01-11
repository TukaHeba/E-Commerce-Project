<?php

namespace App\Models\Category;

use App\Models\Product\Product;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MainCategorySubCategory extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'maincategory_subcategory';

    /**
     * Get the main category associated with maincategory_subcategory record.
     *
     * Defines a belongs-to relationship using the 'main_category_id' foreign key.
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function mainCategory()
    {
        return $this->belongsTo(MainCategory::class, 'main_category_id');
    }

    /**
     * Get the sub category associated with maincategory_subcategory record.
     *
     * Defines a belongs-to relationship using the 'sub_category_id' foreign key.
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function subCategory()
    {
        return $this->belongsTo(SubCategory::class, 'sub_category_id');
    }

    /**
     * Get all products associated with maincategory_subcategory record.
     *
     * Defines a has-many relationship with the Product model.
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
