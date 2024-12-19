<?php

namespace App\Services\User;

use Exception;
use App\Models\User\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\QueryException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
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
    public function getUsers($request)
    {
        try {
            return User::paginate(10);
        } catch (Exception $e) {
             Log::error('Failed to retrieve users: ' . $e->getMessage());
            throw $e;
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
        }catch (QueryException $e) {
            Log::error('User creation failed: ' . $e->getMessage());
            throw $e;
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
            $user->update(array_filter($data));
            return $user;
        }catch (ModelNotFoundException $e) {
            Log::error('User update failed: ' . $e->getMessage());
            throw $e;
        } catch (QueryException $e) {
            Log::error('User update failed: ' . $e->getMessage());
            throw $e;
        }
    }

}
