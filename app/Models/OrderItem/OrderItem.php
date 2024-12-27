<?php

namespace App\Models\OrderItem;

use App\Models\Order\Order;
use App\Models\Product\Product;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OrderItem extends Model
{
  use HasFactory, SoftDeletes;

  /**
   * The attributes that are mass assignable.
   *
   * @var array<int, string>
   */
  protected $fillable = [
    'order_id',
    'product_id',
    'quantity',
    'price'
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
    'quantity' => 'integer',
  ];

  /**
   * Get the order associated with the order item.
   * 
   * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
   */
  public function order()
  {
    return $this->belongsTo(Order::class);
  }

  /**
   * Get the product associated with the order item.
   * 
   * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
   */
  public function product()
  {
    return $this->belongsTo(Product::class);
  }
}
