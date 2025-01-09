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
    return $this->belongsToMany(MainCategory::class, 'maincategory_subcategory');
  }

  public function photos()
  {
    return $this->morphMany(Photo::class, 'photoable');
  }
   /**
     * Resolve route model binding with soft-deleted models.
     * Overrides the default route model binding to include soft-deleted models.
     *
     * @param mixed  $value The value of the binding (e.g., ID).
     * @param string|null $field The field to search for (defaults to 'id').
     * @return \Illuminate\Database\Eloquent\Model The resolved model instance.
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException If the model is not found.
     */
    public function resolveRouteBinding($value, $field = null)
    {
        return $this->where($field ?? 'id', $value)->withTrashed()->firstOrFail();
    }
}
