<?php

namespace App\Models\Product;

use App\Models\Rate\Rate;
use App\Models\User\User;
use App\Models\Photo\Photo;
use InvalidArgumentException;
use App\Exports\LowStockExport;
use App\Models\CartItem\CartItem;
use App\Models\Category\Category;
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

    /**
     * The attributes that are not mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * Relations
     */

    /**
     * Get the users who favored this product.
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
     * Get the rates associated with the product.
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function ratings()
    {
        return $this->hasMany(Rate::class);
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

    /**
     * Scopes
     */

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
     * Scope to retrieve the latest products.
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int $limit The number of latest products to retrieve (default: 30).
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeLatestProducts($query, $limit = 30)
    {
        return $query->orderBy('created_at', 'desc')->take($limit);
    }

    /**
     * Scope to filter available products (quantity > 0).
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeAvailable($query)
    {
        return $query->where('product_quantity', '>', 0);
    }

    /**
     * Scope to get products that a user might like.
     * Avoids products the user has already favored.
     * @param mixed $query
     * @param mixed $user_id
     * @return mixed
     */
    public function scopeMayLikeProducts($query, $user_id)
    {
        $columns = $this->getColumns('product');
        return $query
            ->when($user_id, function ($q) use ($user_id) {
                $q->whereIn('products.maincategory_subcategory_id', function ($subQuery) use ($user_id) {
                    $subQuery->select('products.maincategory_subcategory_id')
                        ->from('favorites')
                        ->join('products', 'favorites.product_id', '=', 'products.id')
                        ->where('favorites.user_id', $user_id);
                })
                    ->whereNotExists(function ($subQuery) use ($user_id) {
                        $subQuery->select(DB::raw(1))
                            ->from('favorites')
                            ->whereRaw('favorites.product_id = products.id')
                            ->where('favorites.user_id', $user_id);
                    });
            })
            ->select($columns)
            ->joinRelatedTables()
            ->distinct();
    }

    /**
     * Functions
     */

    /**
     * Get the average rating for the product.
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
     * Generate a low-stock report in Excel format.
     * @return string Path to the generated file.
     */
    static function generateLowStockReport()
    {
        $fileName = 'reports/low-stock-report-' . now()->format('Y-m-d') . '.xlsx';
        Excel::store(new LowStockExport, $fileName, 'local'); // Save to storage/app

        return $fileName;
    }

    /**
     * Utility functions
     */

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
}
