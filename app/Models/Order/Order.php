<?php

namespace App\Models\Order;

use App\Models\User\User;
use Illuminate\Support\Str;
use App\Models\Address\Zone;
use App\Models\OrderItem\OrderItem;
use Illuminate\Database\Eloquent\Model;
use App\Models\OrderTracking\OrderTracking;
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
        'zone_id',
        'postal_code',
        'status',
        'total_price',
    ];

    /**
     * The attributes that are not mass assignable.
     *
     * @var array
     */
    protected $guarded = [
        'order_number',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'total_price' => 'float',
    ];

    /**
     * Boot the model and set a callback for the "creating" event.
     *
     * This adds a callback that generates a unique order number before the order is saved.
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($order) {
            $order->order_number = self::generateOrderNumber();
        });
    }

    /**
     * Generate a unique order number.
     *
     * The order number consists of a prefix, the current time (with microseconds),
     * and a random string to ensure uniqueness. It also checks the database to
     * avoid duplicate order numbers.
     *
     * @return string The generated order number.
     */
    protected static function generateOrderNumber()
    {
        do {
            $prefix = 'NUM';
            $time = now()->format('Hisu');
            $randomString = Str::random(5);

            $orderNumber = "{$prefix}-{$time}-{$randomString}";
        } while (self::where('order_number', $orderNumber)->exists());

        return $orderNumber;
    }

    /**
     * Get the order trackings associated with the order.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
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
     * Get the zone that the order belongs to
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function zone()
    {
        return $this->belongsTo(Zone::class);
    }

    /**
     * Scope to filter orders by shipping_address through LIKE, status & total_price within a range.
     *
     * @param mixed $query
     * @param mixed $request
     * @return mixed
     */
    public function scopeByFilters($query, $request)
    {
        return $query
            ->when($request->order_number, function ($query) use ($request) {
                $query->where('order_number', 'LIKE', "%{$request->order_number}%");
            })
            ->when($request->status, function ($query) use ($request) {
                $query->where('status', $request->status);
            })
            ->when($request->min_price || $request->max_price, function ($query) use ($request) {
                $minPrice = $request->min_price ?? 0;
                $maxPrice = $request->max_price ?? PHP_INT_MAX;
                $query->whereBetween('total_price', [$minPrice, $maxPrice]);
            });
    }
}
