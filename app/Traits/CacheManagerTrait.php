<?
namespace App\Traits;

use Illuminate\Support\Facades\Cache;

trait CacheManagerTrait
{
     /**
     * Generate a unique cache key using a base string and parameters.
     *
     * @param string $base   The base string for the cache key.
     * @param array  $params An array of parameters to include in the key.
     * @return string The generated cache key.
     */
    public static function generateCacheKey(string $base, array $params): string
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
    public static function addCacheKey(string $groupKey, string $cacheKey): void
    {
        $cacheKeys = Cache::get($groupKey, []);
        if (!in_array($cacheKey, $cacheKeys)) {
            $cacheKeys[] = $cacheKey;
            Cache::put($groupKey, $cacheKeys);
        }
    }

    /**
     * Clear all rate cache keys.
     * Removes all cached entries related to rates and clears the cache key list.
     *
     * @return void
     */
    public static function clearCacheGroup(string $groupKey): void
    {
        $cacheKeys = Cache::get($groupKey, []);
        foreach ($cacheKeys as $cacheKey) {
            Cache::forget($cacheKey);
        }
        Cache::forget($groupKey);
    }
}
