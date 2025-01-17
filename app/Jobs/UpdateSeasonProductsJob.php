<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use App\Models\Product\Product;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class UpdateSeasonProductsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     * get the best-selling products last year and same this mounth and store it cache.
     */
    public function handle(): void
    {
        $cacheKey = 'season_products';

        try {
            $products = Product::bestSelling('season')->available()->paginate(10);

            Cache::put($cacheKey, $products, now()->addMonth());
        } catch (\Exception $e) {
            Log::error('Failed to update cache: ' . $e->getMessage());
        }
    }
}
