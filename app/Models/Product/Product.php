<?php

namespace App\Models\Product;

use App\Models\Category\Category;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
      'name',
      'description',
      'price',
      'product_quantity',
      'category_id'
    ];

    protected $hidden = ['product_quantity'];
    /**
     * The attributes that are not mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
      //
    ];
    /**
     * relation with category .
     * each product belongs to one category
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function category()
    {
      return $this->belongsTo(Category::class, 'category_id');
    }

    /**
     * Scope to filter products by category.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int $category_id - The ID of the category to filter by.
     * @param int $limit - The number of products to retrieve (default is 25).
     * @return \Illuminate\Database\Eloquent\Builder - Returns the query filtered by category.
     */
    public function scopeByCategory($query, $category_id, $limit = 25)
    {
        return $query->where('category_id', $category_id)->take($limit);
    }

    /**
     * Scope to retrieve the latest products.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int $limit - The number of latest products to retrieve (default is 25).
     * @return \Illuminate\Database\Eloquent\Builder - Returns the query ordered by creation date, limited to the specified number.
     */
    public function scopeLatestProducts($query, $limit = 25)
    {
        return $query->orderBy('created_at', 'desc')->take($limit);
    }

    /**
     * Scope to filter products based on multiple criteria: price order, name, category, and availability.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string|null $price - Order by price ('asc' or 'desc').
     * @param string|null $name - Search for products containing this name (partial match).
     * @param int|null $category_id - Filter by category ID.
     * @param bool $latest - Whether to order by the latest products first (default is false).
     * @param int $limit - The number of products to retrieve (default is 25).
     * @return \Illuminate\Database\Eloquent\Builder - Returns the filtered query based on the specified criteria.
     */
    public function scopeFilterProducts($query, $price = null, $name = null, $category_id = null, $latest = false, $limit = 25)
    {
        return $query
            ->when($name, fn($q) => $q->where('name', 'LIKE', '%' . $name . '%'))
            ->when($category_id, fn($q) => $q->where('category_id', $category_id))
            ->when($price && in_array($price, ['asc', 'desc']), fn($q) => $q->orderBy('price', $price))
            ->when($latest, fn($q) => $q->orderBy('created_at', 'desc'))
            ->where('product_quantity', '>', 0)
            ->take($limit);
    }

    /**
     * Scope to filter only available products.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder - Returns the query filtered by products with quantity greater than 0.
     */
    public function scopeAvailable($query)
    {
        return $query->where('product_quantity', '>', 0);
    }

}
