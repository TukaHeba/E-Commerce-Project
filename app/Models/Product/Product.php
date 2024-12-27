<?php

namespace App\Models\Product;

use App\Models\Rate\Rate;
use App\Models\Photo\Photo;
use App\Models\CartItem\CartItem;
use App\Models\Category\Category;
use Illuminate\Support\Facades\DB;
use App\Models\Category\SubCategory;
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
        'sub_category_id'
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
        return $this->belongsTo(SubCategory::class, 'sub_category_id');
    }
 /**
     * Get the rates of products.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function rates(){
        return $this->hasMany(Rate::class);
    }
    /**
     * Scope to filter products by category.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int $category_id - The ID of the category to filter by.
     * @param int $limit - The number of products to retrieve (default is 25).
     * @return \Illuminate\Database\Eloquent\Builder - Returns the query filtered by category.
     */
    public function scopeByCategory($query, $sub_category_id, $limit = 30)
    {
        return $query->where('sub_category_id', $sub_category_id)->take($limit);
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
                $q->whereIn('products.sub_category_id', function ($subQuery) use ($request) {
                    $subQuery->select('products.sub_category_id')
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
                $q->where('products.sub_category_id', $request->category_id);
            })
            ->where('product_quantity', '>', 0)
            ->leftJoin('order_items', 'products.id', '=', 'order_items.product_id')
            ->leftJoin('sub_categories', 'products.sub_category_id', '=', 'sub_categories.id')
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
            ->leftJoin('sub_categories', 'products.sub_category_id', '=', 'sub_categories.id')
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
     * جلب منتجات فئات المنتجات التي اعجب بها المستخدم / للحذف /
     * @param mixed $query
     * @param mixed $user_id
     * @return mixed
     */
    public function scopeMayLikeProducts($query, $user_id)
    {
        return $query
            ->when($user_id, function ($q) use ($user_id) {               // get categories of products that user like it .
                $q->whereIn('products.sub_category_id', function ($subQuery) use ($user_id) {
                    $subQuery->select('products.sub_category_id')
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
            ->join('sub_categories', 'products.sub_category_id', '=', 'sub_categories.id')     // get products belonf to these categories
            ->distinct();                                                          // to avoid repeate products if user like many products belongs to same category.
    }


    /**
     * get cart items for the product
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function cartItems()
    {
        return $this->hasMany(CartItem::class);
    }

    public function photos()
    {
        return $this->morphMany(Photo::class, 'photoable');
    }
}
