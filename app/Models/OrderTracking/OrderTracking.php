<?php

namespace App\Models\OrderTracking;

use App\Models\Order\Order;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrderTracking extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
      'order_id',
      'old_status',
      'new_status'
    ];
    protected $table = 'order_tracking';

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
    public function order(){
        return $this->belongsTo(Order::class,'order_id');
    }

}
