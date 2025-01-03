<?php

namespace App\Services\Rate;

use App\Models\Rate\Rate;
use Illuminate\Support\Facades\Cache;

class RateService
{
    /**
     * Generate a unique cache key using a base string and parameters.
     *
     * @param string $base   The base string for the cache key.
     * @param array  $params An array of parameters to include in the key.
     * @return string The generated cache key.
     */
    private function generateCacheKey(string $base, array $params): string
    {
        return $base . ':' . http_build_query($params);
    }

    /**
     * Add a cache key to the list of keys for tracking and clearing later.
     *
     * Ensures that the provided key is stored in a centralized list of cache keys.
     * If the key already exists in the list, it will not be added again.
     *
     * @param string $cache_key The cache key to add.
     * @return void
     */
    public function addCasheKey(string $cache_key)
    {
        $cache_keys = Cache::get('rate_cache_keys', []);

        if (!in_array($cache_key, $cache_keys)) {
            $cache_keys[] = $cache_key;
            Cache::put('rate_cache_keys', $cache_keys);
        }
    }

    /**
     * Clear all rate cache keys.
     *
     * Removes all cached entries related to rates and clears the cache key list.
     *
     * @return void
     */
    public function clearRateCache()
    {
        $cacheKeys = Cache::get('rate_cache_keys', []);
        foreach ($cacheKeys as $cacheKey) {
            Cache::forget($cacheKey);
        }
        Cache::forget('rate_cache_keys');
    }

    /**
     * Retrieve a paginated list of rates based on the given filter criteria.
     *
     * @param \Illuminate\Http\Request $request The request containing filter parameters.
     * @return \Illuminate\Contracts\Pagination\Paginator Paginated rates.
     */
    public function getRates($request)
    {
        $cache_key = $this->generateCacheKey('rates_filter', $request->all());
        $this->addCasheKey($cache_key);

        return Cache::remember($cache_key, now()->addHour(), function () use ($request) {
            return Rate::rateFilter($request)->with(['user', 'product'])->paginate(10);
        });
    }

    /**
     * Store a new rate in the database.
     *
     * @param array $data The data to create a new rate.
     * @return \App\Models\Rate\Rate The newly created rate with its relations.
     */
    public function storeRate($data)
    {
        $rate = Rate::create($data);
        $this->clearRateCache();
        return $rate->load(['user', 'product']);
    }

    /**
     * Update an existing rate in the database.
     *
     * @param \App\Models\Rate\Rate $rate The rate to update.
     * @param array $data The data to update the rate.
     * @return \App\Models\Rate\Rate The updated rate with its relations.
     */
    public function updateRate($rate, $data)
    {
        $rate->rating = $data['rating'] ?? $rate->rating;
        $rate->review = $data['review'] ?? $rate->review;
        $rate->save();
        $this->clearRateCache();
        return $rate->load(['user', 'product']);
    }

    /**
     * Soft delete a rate.
     *
     * @param \App\Models\Rate\Rate $rate The rate to delete.
     * @return void
     */
    public function destroy(Rate $rate)
    {
        $rate->delete();
        $this->clearRateCache();
    }

    /**
     * Retrieve a paginated list of soft-deleted rates.
     *
     * @return \Illuminate\Contracts\Pagination\Paginator Paginated soft-deleted rates.
     */
    public function showDeleted()
    {
        $cache_key = 'deleted-rates';
        $this->addCasheKey($cache_key);

        return Cache::remember($cache_key, now()->addHour(), function () {
            return Rate::onlyTrashed()->with(['user', 'product'])->paginate(10);
        });
    }

    /**
     * Restore a soft-deleted rate.
     *
     * @param \App\Models\Rate\Rate $rate The soft-deleted rate to restore.
     * @return \App\Models\Rate\Rate The restored rate with its relations.
     */
    public function restoreDeleted(Rate $rate)
    {
        $rate->restore();
        $this->clearRateCache();
        return $rate->load(['user', 'product']);
    }
}
