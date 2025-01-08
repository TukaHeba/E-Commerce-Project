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
            'user_id' => 4,
            'product_id' => 1,
            'rating' => 3,
            'review' => 'good'
        ]);
        Rate::create([
            'user_id' => 4,
            'product_id' => 20,
            'rating' => 4,
            'review' => 'very good'
        ]);

        Rate::create([
            'user_id' => 13,
            'product_id' => 1,
            'rating' => 5,
            'review' => 'exellent'
        ]);
        Rate::create([
            'user_id' => 13,
            'product_id' => 11,
            'rating' => 1,
            'review' => 'bad'
        ]);
        Rate::create([
            'user_id' => 13,
            'product_id' => 22,
            'rating' => 2,
            'review' => 'good'
        ]);


        Rate::create([
            'user_id' => 25,
            'product_id' => 22,
            'rating' => 3,
            'review' => 'very good'
        ]);

        Rate::create([
            'user_id' => 25,
            'product_id' => 1,
            'rating' => 4,
            'review' => 'exellent'
        ]);


        Rate::create([
            'user_id' => 30,
            'product_id' => 2,
            'rating' => 2,
            'review' => 'bad'
        ]);
        Rate::create([
            'user_id' => 30,
            'product_id' => 15,
            'rating' => 4,
            'review' => 'good'
        ]);

        Rate::create([
            'user_id' => 42,
            'product_id' => 15,
            'rating' => 3,
            'review' => 'very good'
        ]);
        Rate::create([
            'user_id' => 42,
            'product_id' => 33,
            'rating' => 4,
            'review' => 'exellent'
        ]);

        Rate::create([
            'user_id' => 6,
            'product_id' => 2,
            'rating' => 2,
            'review' => 'bad'
        ]);
        Rate::create([
            'user_id' => 6,
            'product_id' => 33,
            'rating' => 2,
            'review' => 'bad'
        ]);

        Rate::create([
            'user_id' => 50,
            'product_id' => 1,
            'rating' => 3,
            'review' => 'very good'
        ]);
    }
}
