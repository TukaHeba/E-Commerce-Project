<?php

namespace Database\Seeders;

use App\Models\Rate\Rate;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class RateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Rate::create([
            'user_id' => 1,
            'product_id' =>1,
            'rating' => 3,
            'review' => 'good'
        ]);
        Rate::create([
            'user_id' => 1,
            'product_id' =>2,
            'rating' => 4,
            'review' => 'very good'
        ]);
        Rate::create([
            'user_id' => 2,
            'product_id' =>1,
            'rating' => 5,
            'review' => 'exellent'
        ]);
        Rate::create([
            'user_id' => 2,
            'product_id' =>2,
            'rating' => 1,
            'review' => 'bad'
        ]);
        Rate::create([
            'user_id' => 3,
            'product_id' =>1,
            'rating' => 2,
            'review' => 'good'
        ]);
        Rate::create([
            'user_id' => 3,
            'product_id' =>2,
            'rating' => 3,
            'review' => 'very good'
        ]);
        Rate::create([
            'user_id' => 4,
            'product_id' =>1,
            'rating' => 4,
            'review' => 'exellent'
        ]);
        Rate::create([
            'user_id' => 4,
            'product_id' =>2,
            'rating' => 2,
            'review' => 'bad'
        ]);
        Rate::create([
            'user_id' => 5,
            'product_id' =>1,
            'rating' => 4,
            'review' => 'good'
        ]);
        Rate::create([
            'user_id' => 5,
            'product_id' =>2,
            'rating' => 3,
            'review' => 'very good'
        ]);
        Rate::create([
            'user_id' => 6,
            'product_id' =>1,
            'rating' => 4,
            'review' => 'exellent'
        ]);
        Rate::create([
            'user_id' => 6,
            'product_id' =>2,
            'rating' => 2,
            'review' => 'bad'
        ]);
    }
}
