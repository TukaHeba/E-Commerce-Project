<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category\SubCategory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class SubCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        SubCategory::create([
            'sub_category_name' => 'sub1'
        ]);
        SubCategory::create([
            'sub_category_name' => 'sub2'
        ]);
        SubCategory::create([
            'sub_category_name' => 'sub3'
        ]);
        SubCategory::create([
            'sub_category_name' => 'sub4'
        ]);
        SubCategory::create([
            'sub_category_name' => 'sub5'
        ]);
    }
}
