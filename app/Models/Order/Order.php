<?php

namespace App\Models\Order;

use Illuminate\Contracts\Database\Eloquent\Builder;
use App\Models\OrderTracking\OrderTracking;
use App\Models\User\User;
use App\Models\OrderItem\OrderItem;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Order extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'shipping_address',
        'status',
        'total_price',
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
        //
    ];
    public function orderTrackings()
    {
        return $this->hasMany(OrderTracking::class);
    }

    /**
     * Get the order items for the order.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Get the user that owns the order.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope to filter orders by shipping_address, status & total_price.
     * @param mixed $query
     * @param mixed $filters
     * @return \Illuminate\Contracts\Database\Eloquent\Builder
     */
    public function scopeByFilters($query, $filters): Builder
    {
        if (!empty($filters['shipping_address'])) {
            $query->where('shipping_address', $filters['shipping_address']);
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['total_price'])) {
            $query->where('total_price', $filters['total_price']);
        }
        return $query;
    }
}
