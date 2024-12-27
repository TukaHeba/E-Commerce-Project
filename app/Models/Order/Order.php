<?php

namespace App\Models\Order;

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
}
