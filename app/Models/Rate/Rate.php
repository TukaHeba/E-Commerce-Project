<?php

namespace App\Models\Rate;

use App\Models\User\User;
use App\Models\Product\Product;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Rate extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'product_id',
        'rating',
        'review',
    ];

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
        'rating' => 'integer'
    ];
    /**
     * relation with user
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    /**
     * relation with product
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
    /**
     * Resolve route model binding with soft-deleted models.
     * Overrides the default route model binding to include soft-deleted models.
     *
     * @param mixed  $value The value of the binding (e.g., ID).
     * @param string|null $field The field to search for (defaults to 'id').
     * @return \Illuminate\Database\Eloquent\Model The resolved model instance.
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException If the model is not found.
     */
    public function resolveRouteBinding($value, $field = null)
    {
        return $this->where($field ?? 'id', $value)->withTrashed()->firstOrFail();
    }

    /**
     * Apply filters to the Rate query based on request parameters.
     * Allows filtering by `user_id`, `product_id`, and `rating`.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query The query builder instance.
     * @param \Illuminate\Http\Request $request The request containing filter parameters.
     * @return \Illuminate\Database\Eloquent\Builder The query builder with applied filters.
     */
    public function scopeRateFilter($query, $request)
    {
        return $query
            ->when($request->user_id, function ($q) use ($request) {
                $q->where('user_id', $request->user_id);
            })
            ->when($request->product_id, function ($q) use ($request) {
                $q->where('product_id', $request->product_id);
            })
            ->when($request->rating, function ($q) use ($request) {
                $q->where('rating', $request->rating);
            });
    }

}
