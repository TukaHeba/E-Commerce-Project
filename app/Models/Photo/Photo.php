<?php

namespace App\Models\Photo;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Photo extends Model
{
  use HasFactory, SoftDeletes;

  /**
   * The attributes that are mass assignable.
   *
   * @var array<int, string>
   */
  protected $fillable = [
    'url',
    'photoable_id',
    'photoable_type'
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
   * Get the owning photoable model (user, product, or category).
   */
  public function photoable()
  {
    return $this->morphTo();
  }
}
