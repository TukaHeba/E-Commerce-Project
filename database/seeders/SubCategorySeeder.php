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
        $subcategories = [
            'Smartphones', 'Laptops', 'Tablets', 'Wearable Tech', 'Cameras', 'Audio Equipment', // for Electronics
            'Furniture', 'Decor', 'Lighting', 'Storage Solutions', 'Bedding', // for Home
            'Cookware', 'Kitchen Appliances', 'Cutlery', 'Dinnerware', 'Bakeware', // for Kitchen
            'Men\'s Clothing', 'Women\'s Clothing', 'shoses', 'Accessories', 'Activewear', // for Fashion
             'Vitamins & Supplements', 'Health Monitoring Devices', 'First Aid', 'Exercise Equipment', // for Health
             'Skincare', 'Makeup', 'Hair Care', 'Fragrances', 'Nail Care', // for Beauty
             'Fitness Equipment', 'Outdoor Gear', 'Team Sports Gear', 'Athletic Clothing', 'Footwear' // for Sports
             ];

        foreach ($subcategories as $category)
        {
            SubCategory::create(['sub_category_name'=> $category]);
        }

}
}
