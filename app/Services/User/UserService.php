<?php

namespace App\Services\User;

use App\Models\User\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class UserService
{
    /**
     * Retrieve all users with pagination.
     *
     * Fetch paginated users
     * Log the exception and throw it
     * @return LengthAwarePaginator
     */
    public function getUsers()
    {
        return User::paginate(10);
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
        return $user;
    }

    /**
     * Calculate the average total price of delivered orders for a user.
     *
     * @param User $user
     * @return float|null The average total price of delivered orders, or null if none.
     */
    public function calculateAverage(User $user): ?float
    {
        return $user->orders()
            ->where('status', 'delivered')->avg('total_price');
    }

    /**
     * Show deleted users
     * @return mixed
     */
    public function showDeletedUsers()
    {
        $users = User::onlyTrashed()->paginate();
        return $users;
    }
}
