<?php

namespace App\Models\Category;

use App\Models\Photo\Photo;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SubCategory extends Model
{
  use HasFactory, SoftDeletes;
  /**
   * The attributes that are mass assignable.
   *
   * @var array<int, string>
   */
  protected $fillable = [
    'sub_category_name'
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
   * Relation with main category.
   * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
   */
  public function mainCategories()
  {
    return $this->belongsToMany(MainCategory::class, 'maincategory_subcategory')->withTimestamps();
  }

  public function photos()
  {
      return $this->morphMany(Photo::class, 'photoable');
  }

}
