<?php

namespace App\Models\Category;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MainCategorySubCategory extends Model
{
    use HasFactory, SoftDeletes;

   protected $table = 'maincategory_subcategory';
}
