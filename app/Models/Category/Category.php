<?php

namespace App\Models\Category;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model
{
  use HasFactory, SoftDeletes;

  /**
   * The attributes that are mass assignable.
   *
   * @var array<int, string>
   */
  protected $fillable = [
    //
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

  // Main categories associated with subcategories
  public function mainCategories()
  {
    return $this->belongsToMany(Category::class, 'category_category', 'main_category_id', 'category_id');
  }

  // Subcategories pointing to their main categories
  public function subCategories()
  {
    return $this->belongsToMany(Category::class, 'category_category', 'category_id', 'main_category_id');
  }
}
