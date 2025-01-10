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
        $MainCategories = [
            'Electronics',
            'Home',
            'Kitchen',
            'Fashion',
            'Health',
            'Beauty',
            'Sports',
        ];


        foreach ($MainCategories as $category) {
            MainCategory::create(['main_category_name' => $category]);
        }
    }
}
