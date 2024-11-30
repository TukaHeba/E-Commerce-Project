<?php

namespace App\Services\User;

use App\Models\User\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Log;

class UserService
{
    /**
     * Retrieve all users with pagination.
     *
     * Fetch paginated users
     * Log the exception and throw it
     * @return LengthAwarePaginator
     */
    public function getUsers($request)
    {
        try {
            return User::paginate(10);
        } catch (\Exception $e) {
            Log::error('Failed to retrieve users: ' . $e->getMessage());
            throw new \Exception('An error occurred on the server.');
        }
    }

    /**
     * Create a new user with the provided data.
     *
     * @param array $data The validated data to create a user.
     * @return User|null The created user object on success, or null on failure.
     * @throws \Exception
     */
    public function storeUser(array $data): ?User
    {
        try {
            $user = User::create($data);
            return $user;
        } catch (\Exception $e) {
            Log::error('User creation failed: ' . $e->getMessage());
            throw new \Exception('An error occurred on the server.');
        }
    }

    /**
     * Update an existing user with the provided data.
     *
     * @param User $user The user to update.
     * @param array $data The validated data to update the user.
     * @return User|null The updated user object on success, or null on failure.
     * @throws \Exception
     */
    public function updateUser(User $user, array $data): ?User
    {
        try {
            $user->update($data);
            return $user;
        } catch (\Exception $e) {
            Log::error('User update failed: ' . $e->getMessage());
            throw new \Exception('An error occurred on the server.');
        }
    }

}
