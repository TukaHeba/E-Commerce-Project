<?php

namespace App\Http\Controllers\User;

use App\Models\User\User;
use App\Traits\CacheManagerTrait;
use Illuminate\Http\JsonResponse;
use App\Services\User\UserService;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Http\Requests\User\StoreUserRequest;
use App\Http\Requests\User\UpdateUserRequest;


class UserController extends Controller
{
    use CacheManagerTrait;
    private $groupe_key_cache = 'users_cache_keys';
    protected UserService $UserService;

    public function __construct(UserService $UserService)
    {
        $this->UserService = $UserService;
    }
    /**
     * Display a listing of users.
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(): JsonResponse
    {
        $this->authorize('index', User::class);
        $users = $this->UserService->getUsers();
        return self::paginated($users, UserResource::class, 'Users retrieved successfully', 200);

    }

    /**
     * Create a new user and store it in the database.
     *
     * @param \App\Http\Requests\User\StoreUserRequest $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function store(StoreUserRequest $request): JsonResponse
    {
        $this->authorize('store', User::class);
        $user = $this->UserService->storeUser($request->validated());
        return self::success(new UserResource($user), 'User created successfully', 201);
    }

    /**
     * Display details of a specific user.
     *
     * @param \App\Models\User\User $user
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(User $user): JsonResponse
    {
        $this->authorize('show', $user);
        return self::success(new UserResource($user), 'User retrieved successfully');
    }

    /**
     * Update the details of an existing user.
     *
     * @param \App\Http\Requests\User\UpdateUserRequest $request
     * @param \App\Models\User\User $user
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function update(UpdateUserRequest $request, User $user): JsonResponse
    {
        $this->authorize('update', $user);
        $updatedUser = $this->UserService->updateUser($user, $request->validated());
        return self::success(new UserResource($updatedUser), 'User updated successfully');
    }

    /**
     * Remove  user from the database (soft delete).
     *
     * @param \App\Models\User\User $user
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(User $user): JsonResponse
    {
        $this->authorize('delete', $user);
        $user->delete();
        $this->clearCacheGroup($this->groupe_key_cache);
        return self::success(null, 'User deleted successfully');
    }

    /**
     * Display all soft-deleted users.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function showDeleted(): JsonResponse
    {
        $this->authorize('showDeleted', User::class);
        $users = $this->UserService->showDeletedUsers();
        return self::paginated($users, UserResource::class, 'Users retrieved successfully', 200);
    }
    /**
     * Restore a soft-deleted user.
     *
     * @param User $user  the user to restore.
     * @return \Illuminate\Http\JsonResponse
     */
    public function restoreDeleted($user): JsonResponse
    {
        $this->authorize('restoreDeleted', User::class);
        $user = User::onlyTrashed()->findOrFail($id);
        $user->restore();
        $this->clearCacheGroup($this->groupe_key_cache);
        return self::success(null, 'User restored successfully');
    }
    /**
     * Permanently delete a soft-deleted user.
     *
     * @param  User $user   the user to permanently delete.
     * @return \Illuminate\Http\JsonResponse
     */
    public function forceDeleted($user): JsonResponse
    {
        $this->authorize('forceDeleted', User::class);
        User::onlyTrashed()->findOrFail($id)->forceDelete();
        $this->clearCacheGroup($this->groupe_key_cache);
        return self::success(null, 'User force deleted successfully');
    }

    /**
     * Calculate the average total price of all delivered orders for the user.
     *
     * @param User $user
     * @return \Illuminate\Http\JsonResponse
     *
     */

    public function userPurchasesAverage($user)
    {
        $userPurchasesAverage = $this->UserService->userPurchasesAverage($user);
        return self::success($userPurchasesAverage, 'the average total price of all delivered orders for the user');
    }

}
