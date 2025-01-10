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
        'created_at',
        'updated_at',
        'deleted_at'
    ];
  /**
   * The attributes that should be cast.
   *
   * @var array<string, string>
   */
  protected $casts = [
    //
  ];
  /**
   * Relation with sub category.
   * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
   */
  public function subCategories()
  {
    return $this->belongsToMany(SubCategory::class, 'maincategory_subcategory')->withTimestamps();
  }




}
