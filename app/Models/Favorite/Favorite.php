<?php

namespace App\Models\Favorite;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Favorite extends Model
{
  use HasFactory;

  /**
   * The database table used by the model.
   *
   * This model represents the pivot table for the many-to-many relationship between users and products.
   * The table is explicitly named 'favorites' instead of the conventional 'product_user' 
   * to better reflect its purpose of storing users' favorite products.
   * 
   * @var string
   */
  protected $table = 'favorites';

  /**
   * The attributes that are mass assignable.
   *
   * @var array<int, string>
   */
  protected $fillable = [
    'user_id',
    'product_id'
  ];

  /**
   * The attributes that should be hidden for serialization.
   *
   * @var array<int, string>
   */
  protected $hidden = [
    'created_at',
    'updated_at',
  ];
}
