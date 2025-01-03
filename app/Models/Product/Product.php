<?php

namespace App\Models\Product;

use App\Models\Rate\Rate;
use App\Models\User\User;
use App\Models\Photo\Photo;
use App\Models\CartItem\CartItem;
use App\Models\Category\Category;
use Illuminate\Support\Facades\DB;
use App\Models\OrderItem\OrderItem;
use App\Models\Category\SubCategory;
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
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function favoredBy()
    {
        return $this->belongsToMany(User::class, 'favorites')->withTimestamps();
    }

    /**
     * relation with category .
     * each product belongs to one category
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function category()
    {
        return $this->belongsTo(MainCategorySubCategory::class, 'maincategory_subcategory_id');
    }
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
     * Get the rates of products.
     *
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
            return $this->ratings()->avg('rating') ?? 0;
        });
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
        return $query
            ->when($request->name, function ($q) use ($request) {
                $q->where('products.name', 'LIKE', '%' . $request->name . '%');
            })
            ->when($request->user_id, function ($q) use ($request) {
                $q->whereIn('products.maincategory_subcategory_id', function ($subQuery) use ($request) {
                    $subQuery->select('products.maincategory_subcategory_id')
                        ->from('favorites')
                        ->join('products', 'favorites.product_id', '=', 'products.id')
                        ->where('favorites.user_id', $request->user_id);
                });
            })
            ->when($request->price && in_array($request->price, ['asc', 'desc']), function ($q) use ($request) {
                $q->orderBy('products.price', $request->price);
            })
            ->when($request->latest, function ($q) {
                $q->orderBy('products.created_at', 'desc');
            })
            ->when($request->category_id, function ($q) use ($request) {
                $q->where('products.maincategory_subcategory_id', $request->category_id);
            })
            ->where('product_quantity', '>', 0)
            ->leftJoin('order_items', 'products.id', '=', 'order_items.product_id')
            ->leftJoin('sub_categories', 'products.maincategory_subcategory_id', '=', 'sub_categories.id')
            ->select(
                'products.id',
                'products.name',
                'products.description',
                'products.price',
                'sub_categories.sub_category_name as category_name',
                DB::raw('COALESCE(SUM(order_items.quantity), 0) as total_sold')
            )
            ->groupBy('products.id', 'products.name', 'products.description', 'products.price', 'sub_categories.sub_category_name')
            ->orderByDesc('total_sold')
            ->take(100);
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
            ->leftJoin('order_items', 'products.id', '=', 'order_items.product_id')
            ->leftJoin('sub_categories', 'products.maincategory_subcategory_id', '=', 'sub_categories.id')
            ->select(
                'products.id',
                'products.name',
                'products.description',
                'products.price',
                'sub_categories.sub_category_name as category_name',
                DB::raw('COALESCE(SUM(order_items.quantity), 0) as total_sold')
            )
            ->groupBy('products.id', 'products.name', 'products.description', 'products.price', 'sub_categories.sub_category_name')
            ->orderByDesc('total_sold')
            ->take(30);
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
        return $query
            ->when($user_id, function ($q) use ($user_id) {               // get categories of products that user like it .
                $q->whereIn('products.maincategory_subcategory_id', function ($subQuery) use ($user_id) {
                    $subQuery->select('products.maincategory_subcategory_id')
                        ->from('favorites')
                        ->join('products', 'favorites.product_id', '=', 'products.id')     // I used join becouse it faster than with relation .
                        ->where('favorites.user_id', $user_id);
                })
                    ->whereNotExists(function ($subQuery) use ($user_id) {     // to avoid show products allready user liked it .
                        $subQuery->select(DB::raw(1))
                            ->from('favorites')
                            ->whereRaw('favorites.product_id = products.id')
                            ->where('favorites.user_id', $user_id);
                    });
            })
            ->select(
                'products.id',
                'products.name',
                'products.description',
                'products.price',
                'sub_categories.sub_category_name as category_name'
            )
            ->join('sub_categories', 'products.maincategory_subcategory_id', '=', 'sub_categories.id')     // get products belonf to these categories
            ->distinct();                                                          // to avoid repeate products if user like many products belongs to same category.
    }
    /**
     * Scope to filter products with low stock.
     *
     * Filters products where `product_quantity` is less than the given threshold.
     * Useful for generating low-stock reports or alerts.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int $threshold The stock threshold (default: 10).
     * 
     * @return \Illuminate\Database\Eloquent\Builder
     *
     * @example
     * Product::lowStock()->get();       // Default threshold: 10
     * Product::lowStock(5)->get();      // Custom threshold: 5
     */
    public function scopeLowStock($query, $threshold = 10)
    {
        return $query->where('product_quantity', '<', $threshold);
    }


    /**
     * Get the cart items associated with the product.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function cartItems()
    {
        return $this->hasMany(CartItem::class);
    }

    /**
     * Get the photos associated with the product.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function photos()
    {
        return $this->morphMany(Photo::class, 'photoable');
    }

    /**
     * Get the order items associated with the product.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }









    public function scopeSelling($query)
    {
        return $query
            ->leftJoin('order_items', 'products.id', '=', 'order_items.product_id')
            ->leftJoin('maincategory_subcategory', 'products.maincategory_subcategory_id', '=', 'maincategory_subcategory.id')
            ->leftJoin('sub_categories', 'maincategory_subcategory.sub_category_id', '=', 'sub_categories.id')
            ->leftJoin('main_categories', 'maincategory_subcategory.main_category_id', '=', 'main_categories.id')
            ->select(
                'products.id',
                'sub_categories.sub_category_name as sub_category_name',
                'main_categories.main_category_name as main_category_name',
                DB::raw('COALESCE(SUM(order_items.quantity), 0) as total_sold')
            )
            ->groupBy('products.id', 'sub_categories.sub_category_name', 'main_categories.main_category_name')
            ->orderByDesc('total_sold')
            ->take(30);
    }
}
