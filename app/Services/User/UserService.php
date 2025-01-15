<?php

namespace App\Services\User;

use App\Models\User\User;
use App\Services\Photo\PhotoService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class UserService
{
    protected PhotoService $photoService;

    public function __construct(PhotoService $photoService)
    {
        $this->photoService = $photoService;
    }

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
     * @throws \Exception
     */
    public function storeUser(array $data): ?User
    {
        $user = DB::transaction(function () use ($data) {
            $user = User::create($data);
            if (isset($data['avatar'])) {
                $result = $this->photoService->storePhoto($data['avatar'], $user, 'avatars');
            } else {
                $this->photoService->addDefaultAvatar($user);
            }
            return $user;
        });
        return $user;
    }

    /**
     * Update an existing user with the provided data.
     *
     * @param User $user The user to update.
     * @param array $data The validated data to update the user.
     * @return User|null The updated user object on success, or null on failure.
     */
    public function updateUser(User $user, array $data)//: ?User
    {
        $update_user = DB::transaction(function () use ($user, $data) {
            $user->update(array_filter($data));
            if (isset($data['avatar'])) {
                $avatar = $user->avatar;
                if ($avatar) {
                    if ($avatar->photo_path != "avatars/default_avatar.png") {
                        $this->photoService->deletePhoto($avatar->photo_path, $avatar->id);
                    } else {
                        $user->avatar()->delete();
                    }
                }
                $result = $this->photoService->storePhoto($data['avatar'], $user, 'avatars');
            }
            return $user;
        });
        return $update_user;
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

    /**
     * Permanently delete a soft-deleted user with avatar removal.
     *
     * @param $userId
     * @return void
     */
    public function forceDelete($userId)
    {
        $user = User::onlyTrashed()->findOrFail($userId);
        DB::transaction(function () use ($user) {
            $user->avatar()->delete();
            $user->forceDelete();
        });
    }
}
