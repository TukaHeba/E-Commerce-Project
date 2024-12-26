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
            'user_id' => 1,
            'product_id' => 2,
         ]);
         Favorite::create([
            'user_id' => 2,
            'product_id' => 3,
         ]);
         Favorite::create([
            'user_id' => 1,
            'product_id' => 4,
         ]);
    }
}
