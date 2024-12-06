<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category\MainCategory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class MainCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        MainCategory::create([
            'main_category_name' => 'Men'
        ]);
        MainCategory::create([
            'main_category_name' => 'Women'
        ]);
        MainCategory::create([
            'main_category_name' => 'Children'
        ]);
    }
}
