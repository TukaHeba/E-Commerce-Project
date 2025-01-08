<?php

namespace Database\Seeders;

use App\Models\Address\City;
use App\Models\Address\Country;
use App\Models\Address\Zone;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AddressSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // إنشاء دول تحتوي على مدن ومناطق
        Country::factory()
            ->count(12) // 5 دول
            ->has(
                City::factory()
                    ->count(5) // 3 مدن لكل دولة
                    ->has(
                        Zone::factory()->count(8),
                        'zones'
                    ) // 4 مناطق لكل مدينة
            )

            ->create();
    }
}
