<?php

namespace App\Services\User;

use App\Models\User\User;
use App\Traits\CacheManagerTrait;
use Illuminate\Support\Facades\Cache;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class UserService
{
    use CacheManagerTrait;
    private $groupe_key_cache = 'users_cache_keys';

    /**
     * Retrieve all users with pagination.
     *
     * Fetch paginated users
     * Log the exception and throw it
     * @return LengthAwarePaginator
     */
    public function getUsers()
    {
        $cache_key = 'users';
        $this->addCacheKey($this->groupe_key_cache, $cache_key);

        return Cache::remember($cache_key, now()->addDay(), function () {
            return User::paginate(10);
        });
    }

    /**
     * Create a new user with the provided data.
     *
     * @param array $data The validated data to create a user.
     * @return User|null The created user object on success, or null on failure.
     */
    public function storeUser(array $data): ?User
    {
        $user = User::create($data);
        $this->clearCacheGroup($this->groupe_key_cache);
        return $user;
    }

    /**
     * Update an existing user with the provided data.
     *
     * @param User $user The user to update.
     * @param array $data The validated data to update the user.
     * @return User|null The updated user object on success, or null on failure.
     */
    public function updateUser(User $user, array $data): ?User
    {
        $user->update(array_filter($data));
        $this->clearCacheGroup($this->groupe_key_cache);
        return $user;
    }

    /**
     * Calculate the average total price of all delivered orders for the user.
     *
     * @param string $id The ID of the user.
     * @return float|null The average total price of delivered orders. Returns null if there are no delivered orders.
     */
    public function userPurchasesAverage($user)
    {
        $userPurchasesAverage = $user->userPurchasesAverage;
        return $userPurchasesAverage;
    }

    /**
     * Show deleted users
     * @return mixed
     */
    public function showDeletedUsers()
    {
        $cache_key = 'deleted_users';
        $this->addCacheKey($this->groupe_key_cache, $cache_key);

        $users = User::onlyTrashed()->paginate();

        return Cache::remember($cache_key, now()->addWeek(), function () use ($users) {
            return $users;
        });
    }
}
