<?php

namespace App\Models\Product;

use App\Models\Category\Category;
use Illuminate\Support\Facades\DB;
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
    public function scopeByCategory($query, $category_id, $limit = 30)
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
    public function scopeLatestProducts($query, $limit = 30)
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
    public function scopeFilterProducts($query, $price = null, $name = null, $category_id = null, $latest = false, $user_id = null, $limit = 100)
    {
        return $query
            ->when($name, function ($q) use ($name) {
                $q->where('products.name', 'LIKE', '%' . $name . '%');
            })
            ->when($user_id, function ($q) use ($user_id) {
                $q->whereIn('products.category_id', function ($subQuery) use ($user_id) {
                    $subQuery->select('products.category_id')
                            ->from('favorites')
                            ->join('products', 'favorites.product_id', '=', 'products.id')
                            ->where('favorites.user_id', $user_id);
                });
            })
            ->when($price && in_array($price, ['asc', 'desc']), function ($q) use ($price) {
                $q->orderBy('products.price', $price);
            })
            ->when($latest, function ($q) {
                $q->orderBy('products.created_at', 'desc');
            })
            ->when($category_id, function ($q) use ($category_id) {
                $q->where('products.category_id', $category_id);
            })
            ->where('product_quantity', '>', 0)
            ->leftJoin('order_items', 'products.id', '=', 'order_items.product_id')
            ->leftJoin('categories', 'products.category_id', '=', 'categories.id')
            ->select(
                'products.id',
                'products.name',
                'products.description',
                'products.price',
                'categories.name as category_name',
                DB::raw('COALESCE(SUM(order_items.quantity), 0) as total_sold')
            )
            ->groupBy('products.id', 'products.name', 'products.description', 'products.price', 'categories.name')
            ->orderByDesc('total_sold')
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

    public function scopeBestSelling($query)
    {
        return $query
            ->join('order_items', 'products.id', '=', 'order_items.product_id')
            ->leftJoin('categories', 'products.category_id', '=', 'categories.id')
            ->select('products.id', 'products.name', 'products.description', 'products.price','categories.name as category_name', DB::raw('COALESCE(SUM(order_items.quantity), 0) as total_sold'))
            ->groupBy('products.id', 'products.name', 'products.description', 'products.price','categories.name')
            ->having('total_sold', '>', 0)
            ->orderByDesc('total_sold')
            ->take(30);
    }
}
