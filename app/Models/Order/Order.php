<?php

namespace App\Models\Order;

use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

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
        'total_price'
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

    /**
     * Filters related to order
     * @param mixed $query
     * @param mixed $filters
     * @return \Illuminate\Contracts\Database\Eloquent\Builder
     */
    public function scopeFilter($query, $filters): Builder
    {
        if (isset($filters['shipping_address'])) {
            $query->where('shipping_address', $filters['shipping_address']);
        }

        if (isset($filters['total_price'])) {
            $query->where('total_price', $filters['total_price']);
        }

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['show_deleted'])) {
            $query->onlyTrashed();
        }
        return $query;
    }

}
