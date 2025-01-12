<?php

namespace App\Models\User;

use Carbon\Carbon;
use App\Models\Cart\Cart;
use App\Models\Rate\Rate;
use App\Models\Order\Order;
use App\Models\Photo\Photo;
use App\Models\Product\Product;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasOne;
use PHPOpenSourceSaver\JWTAuth\Contracts\JWTSubject;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

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
    protected $guarded = ['telegram_user_id'];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * List of attributes that should be appended to the model's array and JSON representation.
     * These attributes are dynamically generated using accessor methods.
     *
     * @var array
     */
    public $appends = [
        'full_name',
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
     * This method is used to specify the Telegram user ID where 
     * notifications should be sent for this model.
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
     * Get the Oauth for the user.
     * 
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

    /**
     * Accessor to get the full name of the user by concatenating the first and last names.
     * This method is used to create a dynamic attribute 'full_name' when accessing the model.
     *
     * @return string The full name of the user.
     */
    public function getFullNameAttribute()
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    /**
     * Accessor to get the Birth Data with format
     * example: "Saturday, January 10, 1990"
     * @param mixed $value
     * @return string
     */
    public function getBirthdateAttribute($value)
    {
        return Carbon::parse($value)->format('l, F j, Y');
    }

    /**
     * Get the oldest order for a user.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function oldestOrder(): HasOne
    {
        return $this->hasOne(Order::class)->oldestOfMany();
    }

    /**
     * Get the latest order for a user.
     * 
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
