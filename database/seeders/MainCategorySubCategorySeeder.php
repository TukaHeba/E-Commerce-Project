<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category\MainCategorySubCategory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class MainCategorySubCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        MainCategorySubCategory::create([
            'sub_category_id' => 1,
            'main_category_id' => 1
        ]);
        MainCategorySubCategory::create([
            'sub_category_id' => 2,
            'main_category_id' => 1
        ]);
        MainCategorySubCategory::create([
            'sub_category_id' => 3,
            'main_category_id' => 1
        ]);
        MainCategorySubCategory::create([
            'sub_category_id' => 1,
            'main_category_id' => 2
        ]);
        MainCategorySubCategory::create([
            'sub_category_id' => 2,
            'main_category_id' => 2
        ]);
        MainCategorySubCategory::create([
            'sub_category_id' => 3,
            'main_category_id' => 2
        ]);
        MainCategorySubCategory::create([
            'sub_category_id' => 1,
            'main_category_id' => 3
        ]);
        MainCategorySubCategory::create([
            'sub_category_id' => 2,
            'main_category_id' => 3
        ]);
        MainCategorySubCategory::create([
            'sub_category_id' => 3,
            'main_category_id' => 3
        ]);
    }
}
