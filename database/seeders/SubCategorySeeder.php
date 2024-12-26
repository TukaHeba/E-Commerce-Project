<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Category\SubCategory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class SubCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            //sub categories -----------------------------------
            [
                'sub_category_name' => 'Novels',
            ],
            [
                'sub_category_name' => 'Science',
            ],
            [
                'sub_category_name' => 'Rings',
            ],
            [
                'sub_category_name' => 'Flowers',
            ],
            [
                'sub_category_name' => 'Mugs',
            ],
            [
                'sub_category_name' => 'Clothes',
            ],
            [
                'sub_category_name' => 'Toys',
            ],
            [
                'sub_category_name' => 'Bags',
            ],
            [
                'sub_category_name' => 'Glasses',
            ]
        ];

        foreach ($categories as $category) {
            SubCategory::create($category);
        }
        
        //pivot table ----------------------------------------------------------------------------
        $pivotcategories = [
            [
                'main_category_id' => 6,
                'sub_category_id' => 1,
            ],
            [
                'main_category_id' => 6,
                'sub_category_id' => 2,
            ], 
            [
                'main_category_id' => 5,
                'sub_category_id' => 3,
            ],
            [
                'main_category_id' => 4,
                'sub_category_id' => 4,
            ],
            [
                'main_category_id' => 4,
                'sub_category_id' => 5,
            ],
            [
                'main_category_id' => 1,
                'sub_category_id' => 6,
            ],
            [
                'main_category_id' => 2,
                'sub_category_id' => 6,
            ],
            [
                'main_category_id' => 3,
                'sub_category_id' => 6,
            ],
            [
                'main_category_id' => 3,
                'sub_category_id' => 7,
            ],
            [
                'main_category_id' => 1,
                'sub_category_id' => 8,
            ],
            [
                'main_category_id' => 2,
                'sub_category_id' => 8,
            ],
            [
                'main_category_id' => 1,
                'sub_category_id' => 9,
            ],
            [
                'main_category_id' => 2,
                'sub_category_id' => 9,
            ],
            [
                'main_category_id' => 3,
                'sub_category_id' => 9,
            ]
        ];

        foreach ($pivotcategories as $pivotcategory) {
            DB::table('maincategory_subcategory')->insert($pivotcategory);
        }
    }
}
