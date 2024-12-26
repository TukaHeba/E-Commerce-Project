<?php

namespace Database\Seeders;

use App\Models\Product\Product;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Product::create([
            'name'=>'product_1',
            'description'=>'decription for product_1',
            'price'=>299.99,
            'product_quantity'=>22,
            'sub_category_id'=>1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        Product::create([
            'name'=>'product_2',
            'description'=>'decription for product_2',
            'price'=>415.20,
            'product_quantity'=>44,
            'sub_category_id'=>2,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        Product::create([
            'name'=>'product_3',
            'description'=>'decription for product_3',
            'price'=>299.99,
            'product_quantity'=>31,
            'sub_category_id'=>3,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        Product::create([
            'name'=>'product_4',
            'description'=>'decription for product_4',
            'price'=>399.99,
            'product_quantity'=>25,
            'sub_category_id'=>4,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        Product::create([
            'name'=>'product_5',
            'description'=>'decription for product_5',
            'price'=>412.15,
            'product_quantity'=>15,
            'sub_category_id'=>5,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
