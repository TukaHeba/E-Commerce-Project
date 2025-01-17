<?php

namespace App\Models\Category;

use App\Models\Photo\Photo;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Route;


class MainCategory extends Model
{
  use HasFactory, SoftDeletes;

  /**
   * The attributes that are mass assignable.
   *
   * @var array<int, string>
   */
  protected $fillable = [
    'main_category_name'
  ];

  /**
   * The attributes that should be hidden for serialization.
   *
   * @var array<int, string>
   */
  protected $hidden = [
    'created_at',
    'updated_at',
    'deleted_at'
  ];

  /**
   * Get the sub categories associated with the main category.
   * 
   * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
   */
  public function subCategories()
  {
    return $this->belongsToMany(SubCategory::class, 'maincategory_subcategory')->withTimestamps();
  }

  /**
     * Get the photos associated with the  main category.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function photos()
    {
        return $this->morphMany(Photo::class, 'photoable');
    }
}
