<?php

namespace Database\Seeders;

use App\Models\Product\Product;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {

        // Products with 1st main category and 1-6 subcategories
        Product::create([
            'name' => 'iPhone 14 Pro',
            'description' => 'A powerful device to keep you connected and entertained on the go. Features a high-resolution camera, fast processor, and long battery life.',
            'price' => 299.99,
            'product_quantity' => 22,
            'maincategory_subcategory_id' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        Product::create([
            'name' => 'MacBook Air',
            'description' => 'Perfect for working and learning from anywhere. Lightweight and powerful.',
            'price' => 999.99,
            'product_quantity' => 15,
            'maincategory_subcategory_id' => 2,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        Product::create([
            'name' => 'iPad Pro',
            'description' => 'A versatile tablet for work and play with a stunning display.',
            'price' => 799.99,
            'product_quantity' => 10,
            'maincategory_subcategory_id' => 3,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        Product::create([
            'name' => 'Apple Watch Series 7',
            'description' => 'A sleek smartwatch with advanced health monitoring features.',
            'price' => 399.99,
            'product_quantity' => 8,
            'maincategory_subcategory_id' => 4,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        Product::create([
            'name' => 'Canon EOS R6',
            'description' => 'Capture high-quality images and videos with this professional camera.',
            'price' => 2499.99,
            'product_quantity' => 5,
            'maincategory_subcategory_id' => 5,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        Product::create([
            'name' => 'Sony WH-1000XM4 Headphones',
            'description' => 'Experience exceptional sound quality with these top-of-the-line headphones.',
            'price' => 349.99,
            'product_quantity' => 20,
            'maincategory_subcategory_id' => 6,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Products with 2nd main category and 7-11 subcategories
        Product::create([
            'name' => 'Sofa Set',
            'description' => 'A comfortable sofa set to enhance your living room.',
            'price' => 1299.99,
            'product_quantity' => 7,
            'maincategory_subcategory_id' => 7,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        Product::create([
            'name' => 'Wall Art',
            'description' => 'Add a touch of elegance to your space with this wall art.',
            'price' => 199.99,
            'product_quantity' => 10,
            'maincategory_subcategory_id' => 8,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        Product::create([
            'name' => 'Chandelier',
            'description' => 'Illuminate your home with this beautiful chandelier.',
            'price' => 499.99,
            'product_quantity' => 5,
            'maincategory_subcategory_id' => 9,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        Product::create([
            'name' => 'Closet Organizers',
            'description' => 'Organize your belongings with these practical closet organizers.',
            'price' => 99.99,
            'product_quantity' => 20,
            'maincategory_subcategory_id' => 10,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        Product::create([
            'name' => 'Memory Foam Mattress',
            'description' => 'Enjoy a restful sleep with this memory foam mattress.',
            'price' => 899.99,
            'product_quantity' => 12,
            'maincategory_subcategory_id' => 11,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Products with 3rd main category and 12-16 subcategories
        Product::create([
            'name' => 'Non-stick Frying Pan',
            'description' => 'Perfect for cooking without the mess. Features a durable non-stick coating and comfortable handle.',
            'price' => 39.99,
            'product_quantity' => 25,
            'maincategory_subcategory_id' => 12,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        Product::create([
            'name' => 'Blender',
            'description' => 'Blend your favorite smoothies and shakes with ease.',
            'price' => 79.99,
            'product_quantity' => 15,
            'maincategory_subcategory_id' => 13,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        Product::create([
            'name' => 'Chef\'s Knife Set',
            'description' => 'A set of high-quality chef\'s knives for all your culinary needs.',
            'price' => 199.99,
            'product_quantity' => 10,
            'maincategory_subcategory_id' => 14,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        Product::create([
            'name' => 'Porcelain Dinnerware Set',
            'description' => 'A beautiful dinnerware set for any occasion.',
            'price' => 99.99,
            'product_quantity' => 20,
            'maincategory_subcategory_id' => 15,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        Product::create([
            'name' => 'Silicone Baking Mat',
            'description' => 'Bake like a pro with this silicone baking mat.',
            'price' => 24.99,
            'product_quantity' => 30,
            'maincategory_subcategory_id' => 16,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Products with 4th main category and 17-21 subcategories
        Product::create([
            'name' => 'Men\'s Suit',
            'description' => 'Look sharp in this stylish men\'s suit.',
            'price' => 299.99,
            'product_quantity' => 5,
            'maincategory_subcategory_id' => 17,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        Product::create([
            'name' => 'Women\'s Dress',
            'description' => 'A beautiful dress for any special occasion.',
            'price' => 149.99,
            'product_quantity' => 8,
            'maincategory_subcategory_id' => 18,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        Product::create([
            'name' => 'Running Shoes',
            'description' => 'Run comfortably with these high-performance running shoes.',
            'price' => 99.99,
            'product_quantity' => 12,
            'maincategory_subcategory_id' => 19,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        Product::create([
            'name' => 'Leather Belt',
            'description' => 'Complete your outfit with this classic leather belt.',
            'price' => 49.99,
            'product_quantity' => 20,
            'maincategory_subcategory_id' => 20,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        Product::create([
            'name' => 'Yoga Pants',
            'description' => 'Stay comfortable and flexible in these yoga pants.',
            'price' => 39.99,
            'product_quantity' => 25,
            'maincategory_subcategory_id' => 21,
            'created_at' => now(),
            'updated_at' => now(),
        ]);


        // Products with 5th main category and 22-25 subcategories
        Product::create([
            'name' => 'Vitamin D Supplements',
            'description' => 'Stay healthy with these vitamin D supplements.',
            'price' => 19.99,
            'product_quantity' => 50,
            'maincategory_subcategory_id' => 22,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        Product::create([
            'name' => 'Blood Pressure Monitor',
            'description' => 'Monitor your health with this easy-to-use blood pressure monitor.',
            'price' => 49.99,
            'product_quantity' => 20,
            'maincategory_subcategory_id' => 23,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        Product::create([
            'name' => 'First Aid Kit',
            'description' => 'Be prepared for any emergency with this comprehensive first aid kit.',
            'price' => 29.99,
            'product_quantity' => 35,
            'maincategory_subcategory_id' => 24,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        Product::create([
            'name' => 'Dumbbells',
            'description' => 'Get fit with these high-quality dumbbells.',
            'price' => 59.99,
            'product_quantity' => 25,
            'maincategory_subcategory_id' => 25,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Products with 6th main category and 25-29 subcategories
        Product::create([
            'name' => 'Moisturizing Cream',
            'description' => 'Hydrates and nourishes your skin for a healthy glow. Contains natural ingredients and suitable for all skin types.',
            'price' => 19.99,
            'product_quantity' => 40,
            'maincategory_subcategory_id' => 26,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        Product::create([
            'name' => 'Foundation',
            'description' => 'Achieve a flawless look with this high-quality foundation.',
            'price' => 39.99,
            'product_quantity' => 30,
            'maincategory_subcategory_id' => 27,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        Product::create([
            'name' => 'Shampoo and Conditioner',
            'description' => 'Keep your hair clean and healthy with this shampoo and conditioner.',
            'price' => 24.99,
            'product_quantity' => 25,
            'maincategory_subcategory_id' => 28,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        Product::create([
            'name' => 'Perfume',
            'description' => 'Smell amazing with this delightful perfume.',
            'price' => 59.99,
            'product_quantity' => 35,
            'maincategory_subcategory_id' => 29,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        Product::create([
            'name' => 'Nail Polish Set',
            'description' => 'Make your nails look stunning with this nail polish set.',
            'price' => 14.99,
            'product_quantity' => 50,
            'maincategory_subcategory_id' => 30,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Products with 7th main category and 30-34 subcategories
        Product::create([
            'name' => 'Treadmill',
            'description' => 'Stay active with this reliable treadmill.',
            'price' => 999.99,
            'product_quantity' => 10,
            'maincategory_subcategory_id' => 31,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        Product::create([
            'name' => 'Tent',
            'description' => 'Enjoy the outdoors with this spacious tent.',
            'price' => 199.99,
            'product_quantity' => 15,
            'maincategory_subcategory_id' => 32,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        Product::create([
            'name' => 'Soccer Ball',
            'description' => 'Play like a pro with this high-quality soccer ball.',
            'price' => 29.99,
            'product_quantity' => 50,
            'maincategory_subcategory_id' => 33,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        Product::create([
            'name' => 'Running Shorts',
            'description' => 'Stay comfortable during your workouts with these running shorts.',
            'price' => 19.99,
            'product_quantity' => 40,
            'maincategory_subcategory_id' => 34,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
