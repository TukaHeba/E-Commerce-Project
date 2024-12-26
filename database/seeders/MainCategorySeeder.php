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
        $categories = [
            //main categories -----------------------------------
            [
                'main_category_name' => 'Men',
            ],
            [
                'main_category_name' => 'Women',
            ], 
            [
                'main_category_name' => 'Kids',
            ],
            [
                'main_category_name' => 'Giftes',
            ],
            [
                'main_category_name' => 'Accessories',
            ],
            [
                'main_category_name' => 'Books',
            ]
        ];

        foreach ($categories as $category) {
            MainCategory::create($category);
        }
    }
}
