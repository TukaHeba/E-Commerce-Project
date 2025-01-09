<?php

namespace App\Models\User;

use App\Models\Account\Account;
use App\Models\Cart\Cart;
use App\Models\Favorite\Favorite;
use App\Models\Order\Order;
use App\Models\Photo\Photo;
use App\Models\Product\Product;
use App\Models\Rate\Rate;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use PHPOpenSourceSaver\JWTAuth\Contracts\JWTSubject;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements JWTSubject
{
    use HasFactory, SoftDeletes, HasRoles, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'password',
        'phone',
        'address',
        'is_male',
        'birthdate',
        'telegram_user_id'
    ];

    /**
     * The attributes that are not mass assignable.
     *
     * @var array
     */
    protected $guarded = [];
    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];
    public $appends = [
        'full_name',
        'avatar'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'is_male' => 'boolean',
        'birthdate' => 'date',
    ];


    /**
     * Get the route information for Telegram notifications.
     * This method is used to specify the Telegram user ID where notifications should be sent for this model.
     *
     * @return string|null The Telegram user ID of the notifiable.
     */
    public function routeNotificationForTelegram()
    {
        return $this->telegram_user_id;
    }

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }


    /**
     * Get the Oauth for the user
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function providers()
    {
        return $this->hasMany(Provider::class);
    }


    /**
     * Get the orders for the user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    /**
     * Get the cart for the user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */

    public function cart()
    {
        return $this->hasOne(Cart::class);
    }

    /**
     * Get the rates of products for the user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function rates()
    {
        return $this->hasMany(Rate::class);
    }

    /**
     * Get the user favorite products.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function favoriteProducts()
    {
        return $this->belongsToMany(Product::class, 'favorites')->withTimestamps();
    }




    public function getFullNameAttribute()
    {
        return $this->first_name . ' ' . $this->last_name;
    }

/**
 * Calculate the average total price of all delivered orders for the user.
 *
 * @return float|null The average total price of delivered orders. Returns null if there are no delivered orders.
 */
public function userPurchasesAverage()
{
    return $this->orders()
        ->where('status', 'delivered')
        ->avg('total_price');
}


    /**
     * Get the oldest order.
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function oldestOrder(): HasOne
    {
        return $this->hasOne(Order::class)->oldestOfMany();
    }

    /**
     * Get the latest order.
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function latestOrder(): HasOne
    {
        return $this->hasOne(Order::class)->latestOfMany();
    }

    /**
     * Get the avatar for user
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphOne
     */
    public function avatar()
    {
        return $this->morphOne(Photo::class, 'photoable');
    }
}
