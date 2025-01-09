<?php

namespace App\Models\Address;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class City extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'country_id'
    ];

    /**
     * Get the zones for the city
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function zones(){
        return $this->hasMany(Zone::class);
    }

    /**
     * Get the country that the city belongs to
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function country(){
        return $this->belongsTo(Country::class);
    }
}
