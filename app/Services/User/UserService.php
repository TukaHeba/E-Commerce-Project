<?php

namespace App\Services\User;

use Exception;
use App\Models\User\User;
use App\Models\Order\Order;
use Illuminate\Http\Request;
use App\Services\Photo\PhotoService;
use Illuminate\Support\Facades\Auth;
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
     * Calculate the average total price of all delivered orders for the user.
     *
     * @param string $id The ID of the user.
     * @return float|null The average total price of delivered orders. Returns null if there are no delivered orders.
     */
    public function userPurchasesAverage($user)
    {
        $userPurchasesAverage = $user->userPurchasesAverage;
        return   $userPurchasesAverage;
    }
}
