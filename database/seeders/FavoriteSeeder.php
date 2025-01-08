<?php

namespace Database\Seeders;

use App\Models\Favorite\Favorite;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class FavoriteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */

    public function run(): void
    {
        Favorite::create([
            'user_id' => 4,
            'product_id' => 2,
        ]);
        Favorite::create([
            'user_id' => 4,
            'product_id' => 7,
        ]);
        Favorite::create([
            'user_id' => 4,
            'product_id' => 13,
        ]);

        Favorite::create([
            'user_id' => 12,
            'product_id' => 7,
        ]);
        Favorite::create([
            'user_id' => 12,
            'product_id' => 13,
        ]);

        Favorite::create([
            'user_id' => 20,
            'product_id' => 13,
        ]);
        Favorite::create([
            'user_id' => 20,
            'product_id' => 19,
        ]);
        Favorite::create([
            'user_id' => 20,
            'product_id' => 27,
        ]);


        Favorite::create([
            'user_id' => 33,
            'product_id' => 4,
        ]);
        Favorite::create([
            'user_id' => 33,
            'product_id' => 19,
        ]);
        Favorite::create([
            'user_id' => 33,
            'product_id' => 30,
        ]);


        Favorite::create([
            'user_id' => 40,
            'product_id' => 4,
        ]);
        Favorite::create([
            'user_id' => 40,
            'product_id' => 7,
        ]);
        Favorite::create([
            'user_id' => 40,
            'product_id' => 30,
        ]);
        Favorite::create([
            'user_id' => 40,
            'product_id' => 22,
        ]);


        Favorite::create([
            'user_id' => 52,
            'product_id' => 7,
        ]);
        Favorite::create([
            'user_id' => 52,
            'product_id' => 20,
        ]);
        Favorite::create([
            'user_id' => 52,
            'product_id' => 30,
        ]);
        Favorite::create([
            'user_id' => 52,
            'product_id' => 34,
        ]);
    }
}
