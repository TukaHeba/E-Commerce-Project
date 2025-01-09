<?php

namespace App\Models\Product;

use App\Models\Rate\Rate;
use App\Models\User\User;
use App\Models\Photo\Photo;
use App\Exports\UnsoldExport;
use InvalidArgumentException;
use App\Exports\LowStockExport;
use App\Models\CartItem\CartItem;
use Illuminate\Support\Facades\DB;
use App\Models\OrderItem\OrderItem;
use App\Models\Category\SubCategory;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\Category\MainCategory;
use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Category\MainCategorySubCategory;
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
        'maincategory_subcategory_id'
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
     * Get the users favored this product.
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function favoredBy()
    {
        return $this->belongsToMany(User::class, 'favorites')->withTimestamps();
    }

    /**
     * Relation with category: each product belongs to one category.
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function category()
    {
        return $this->belongsTo(MainCategorySubCategory::class, 'maincategory_subcategory_id');
    }
    /**
     * Get the main category associated with the product.
     * @return \Illuminate\Database\Eloquent\Relations\HasOneThrough
     */
    public function mainCategory()
    {
        return $this->hasOneThrough(
            MainCategory::class,
            MainCategorySubCategory::class,
            'id',
            'id',
            'maincategory_subcategory_id',
            'main_category_id'
        );
    }
    /**
     * Get the subcategory associated with the product.
     * @return \Illuminate\Database\Eloquent\Relations\HasOneThrough
     */
    public function subCategory()
    {
        return $this->hasOneThrough(
            SubCategory::class,
            MainCategorySubCategory::class,
            'id',
            'id',
            'maincategory_subcategory_id',
            'sub_category_id'
        );
    }

    /**
     * Get the cart items associated with the product.
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function cartItems()
    {
        return $this->hasMany(CartItem::class);
    }

    /**
     * Get the photos associated with the product.
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function photos()
    {
        return $this->morphMany(Photo::class, 'photoable');
    }

    /**
     * Get the order items associated with the product.
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Get the rates associated with the product.
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function ratings()
    {
        return $this->hasMany(Rate::class);
    }

    /**
     * Get the average rating for the product.
     *
     * @return float The average rating of the product.
     */
    public function averageRating(): float
    {
        $cacheKey = "product_avg_rating_{$this->id}";

        return Cache::remember($cacheKey, now()->addMinutes(30), function () {
            $avgRating = $this->ratings()->avg('rating') ?? 0;
            return round($avgRating, 2);
        });
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

    /**
     * scope to get TopRated products
     * @param mixed $query
     * @param int $limit
     * @return mixed
     */
    public function scopeTopRated($query, int $limit = 10)
    {
        return $query->withAvg('ratings', 'rating')
            ->orderByDesc('ratings_avg_rating')
            ->take($limit);
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
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Database\Eloquent\Builder - Returns the filtered query based on the specified criteria.
     */
    public function scopeFilterProducts($query, $request)
    {
        $type = 'product_with_total_sold_and_rating';

        $query = $this->applyFilters($query, $request);
        $query = $this->applyJoins($query);
        return $query
            ->select($this->getColumns($type))
            ->groupBy($this->getGroupByColumns($type))
            ->orderByDesc('total_sold')
            ->take(100);
    }

    /**
     * Scope to filter products by category.
     * @param mixed $query
     * @param mixed $request
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByCategory($query, $request)
    {
        return $query->whereHas('category.mainCategory', function ($query) use ($request) {
            $query->when($request->mainCategoryId, fn($q) => $q->where('main_categories.id', $request->mainCategoryId))
                ->when($request->subCategoryId, fn($q) => $q->where('sub_categories.id', $request->subCategoryId));
        });
    }
    /**
     * Scope to get products may user like it
     * By join products.id with favourites.products_id tables
     * then take the products.category_id and join with categories.id tables
     * to get all products that belong to products category from favourites table
     * @param mixed $query
     * @param mixed $user_id
     * @return mixed
     */
    public function scopeMayLikeProducts($query, $user_id)
    {
        $columns = $this->getColumns('product');
        return $query
            ->when($user_id, function ($q) use ($user_id) {               // Get categories of products that the user likes.
                $q->whereIn('products.maincategory_subcategory_id', function ($subQuery) use ($user_id) {
                    $subQuery->select('products.maincategory_subcategory_id')
                        ->from('favorites')
                        ->join('products', 'favorites.product_id', '=', 'products.id')     // Using join for better performance compared to relations.
                        ->where('favorites.user_id', $user_id);
                })
                    ->whereNotExists(function ($subQuery) use ($user_id) {     // Avoid showing products that the user has already liked.
                    $subQuery->select(DB::raw(1))
                        ->from('favorites')
                        ->whereRaw('favorites.product_id = products.id')
                        ->where('favorites.user_id', $user_id);
                });
            })
            ->select($columns)
            ->joinRelatedTables()
            ->distinct();      // Avoid repeating products if the user likes multiple products from the same category.
    }

    /**
     * scope to get Best Selling products or category
     * @param mixed $query
     * @param mixed $type
     * @return mixed
     */
    public function scopeBestSelling($query, $type)
    {
        $columns = $this->getColumns($type);

        return $query
            ->leftJoin('order_items', 'products.id', '=', 'order_items.product_id')
            ->joinRelatedTables()
            ->select($columns)
            ->groupBy(...$this->getGroupByColumns($type))
            ->orderByDesc('total_sold');
    }
    /**
     * Scope to filter products with low stock.
     *
     * Filters products where `product_quantity` is less than the given threshold.
     * Useful for generating low-stock reports or alerts.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int $threshold The stock threshold (default: 10).
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeLowStock($query, $threshold = 10)
    {
        return $query->where('product_quantity', '<', $threshold);
    }
    /**
     * Scope to filter unsold products.
     *
     * Filters unsold products.
     * Useful for generating products-never-been-sold reports or alerts.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     *
     * @return \Illuminate\Database\Eloquent\Builder
     *
     */
    public function scopeNeverBeenSold($query)
    {
        return $query->whereDoesntHave('orderItems');
    }
    /**
     * generate low stock products excel sheet as report to admin
     * @return string
     */
    static function generateLowStockReport()
    {
        $fileName = 'reports/low-stock-report-' . now()->format('Y-m-d') . '.xlsx';

        Excel::store(new LowStockExport, $fileName, 'local'); // Save to storage/app

        return $fileName;
    }

    public function scopeJoinRelatedTables($query)
    {
        return $query
            ->leftJoin('maincategory_subcategory', 'products.maincategory_subcategory_id', '=', 'maincategory_subcategory.id')
            ->leftJoin('sub_categories', 'maincategory_subcategory.sub_category_id', '=', 'sub_categories.id')
            ->leftJoin('main_categories', 'maincategory_subcategory.main_category_id', '=', 'main_categories.id');
    }

    private function applyJoins($query)
    {
        return $query
            ->leftJoin('order_items', 'products.id', '=', 'order_items.product_id')
            ->leftJoin('sub_categories', 'products.maincategory_subcategory_id', '=', 'sub_categories.id')
            ->leftJoin('main_categories', 'products.maincategory_subcategory_id', '=', 'main_categories.id')
            ->leftJoin('rates', 'products.id', '=', 'rates.product_id');
    }
    private function applyFilters($query, $request)
    {
        return $query
            ->when($request->name, fn($q) => $q->where('products.name', 'LIKE', '%' . $request->name . '%'))
            ->when($request->user_id, function ($q) use ($request) {
                $q->whereIn('products.maincategory_subcategory_id', function ($subQuery) use ($request) {
                    $subQuery->select('products.maincategory_subcategory_id')
                        ->from('favorites')
                        ->join('products', 'favorites.product_id', '=', 'products.id')
                        ->where('favorites.user_id', $request->user_id);
                });
            })
            ->when($request->mainCategoryId, fn($q) => $q->where('main_categories.id', $request->mainCategoryId))
            ->when($request->subCategoryId, fn($q) => $q->where('sub_categories.id', $request->subCategoryId))
            ->where('product_quantity', '>', 0);
    }

    /**
     * Get the columns to select based on the type.
     * @param string $type
     * @return array
     */
    private function getColumns($type)
    {
        $categoryColumns = [
            'sub_categories.sub_category_name as sub_category_name',
            'main_categories.main_category_name as main_category_name'
        ];
        $productColumns = [
            'products.id',
            'products.name',
            'products.description',
            'products.price'
        ];
        $totalSoldColumn = [
            // Use DB::raw to calculate the total quantity of products sold (SUM of `order_items.quantity`) and set it as 'total_sold'.
            // If no records are found, COALESCE ensures a default value of 0 is returned.
            DB::raw('COALESCE(SUM(order_items.quantity), 0) as total_sold')
        ];
        $averageRate = [
            // Use DB::raw to calculate the average rating (AVG of `rates.rating`) and round it to 2 decimal places.
            // COALESCE ensures that if no ratings exist, a default value of 0 is returned. The result is aliased as 'ratings_avg_rating'.
            DB::raw('ROUND(COALESCE(AVG(rates.rating), 0), 2) as ratings_avg_rating')
        ];
        return match ($type) {
            'product' => array_merge($productColumns, $categoryColumns),
            'category' => $categoryColumns,
            'category_with_total_sold' => array_merge($categoryColumns, $totalSoldColumn),
            'product_with_total_sold' => array_merge($productColumns, $categoryColumns, $totalSoldColumn),
            'product_with_total_sold_and_rating' => array_merge($productColumns, $categoryColumns, $totalSoldColumn, $averageRate),
            default => throw new InvalidArgumentException('Invalid type for select columns'),
        };
    }

    /**
     * Get the group by columns based on the type.
     * @param string $type
     * @return array
     */
    private function getGroupByColumns($type)
    {
        return match (true) {
            in_array($type, ['product', 'product_with_total_sold', 'product_with_total_sold_and_rating']) => [
                'products.id',
                'products.name',
                'products.description',
                'products.price',
                'sub_categories.sub_category_name',
                'main_categories.main_category_name'
            ],
            in_array($type, ['category', 'category_with_total_sold']) => [
                'sub_categories.sub_category_name',
                'main_categories.main_category_name'
            ],
            default => throw new InvalidArgumentException('Invalid type for groupBy columns'),
        };
    }

    /**
     * Get the order item with the largest quantity sold by product name.
     * @param string $name
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function largestQuantitySoldByName($name)
    {
        return $this->hasOne(OrderItem::class)
            ->ofMany('quantity', 'max')
            ->whereHas('product', function ($query) use ($name) {
                $query->where('name', 'like', '%' . $name . '%');
            });
    }

}
