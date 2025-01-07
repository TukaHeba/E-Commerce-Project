<?php

namespace App\Models\Address;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name'
    ];


    /**
     * Get the cities for the city
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function cities(){
        return $this->hasMany(City::class);
    }

    /**
     * Get the zones of this city
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough
     */
    public function zones(){
        return $this->hasManyThrough(Zone::class,City::class);
    }

}
