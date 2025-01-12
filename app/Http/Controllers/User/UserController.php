<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\RemoveRoleRequest;
use App\Http\Requests\User\RoleRequest;
use App\Http\Requests\User\StoreUserRequest;
use App\Http\Requests\User\UpdateUserRequest;
use App\Http\Resources\UserResource;
use App\Models\User\User;
use App\Services\User\UserService;
use Illuminate\Http\JsonResponse;
use Spatie\Permission\Models\Role;


class UserController extends Controller
{
    protected UserService $UserService;

    public function __construct(UserService $UserService)
    {
        $this->UserService = $UserService;
    }

    /**
     * Display a listing of users.
     *
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
     * @param User $user the user to restore.
     * @return \Illuminate\Http\JsonResponse
     */
    public function restoreDeleted(User $user): JsonResponse
    {
        $this->authorize('restoreDeleted', User::class);
        $user = User::onlyTrashed()->findOrFail($user->id);
        $user->restore();
        return self::success(null, 'User restored successfully');
    }

    /**
     * Permanently delete a soft-deleted user.
     *
     * @param User $user the user to permanently delete.
     * @return \Illuminate\Http\JsonResponse
     */
    public function forceDeleted($userId): JsonResponse
    {
        $this->authorize('forceDeleted', User::class);
        User::onlyTrashed()->findOrFail($userId)->forceDelete();
        return self::success(null, 'User force deleted successfully');
    }

    /**
     * Get the average total price of delivered orders for a specific user.
     *
     * @param User $user
     * @return JsonResponse
     */
    public function getAveragePurchases(User $user): JsonResponse
    {
        $average = $this->UserService->calculateAverage($user);

        if (is_null($average)) {
            return self::error(null, 'No delivered orders found for this user', 404);
        }
        return self::success($average, 'The average of all this user\'s completed orders is:');
    }

    /**
     * Assign a role to a user.
     *
     * @param RoleRequest $request
     * @param User $user
     * @return JsonResponse
     */
    public function assignRole(User $user,Role $role)
    {
        $user->assignRole($role);
        return self::success(null, 'The role has been added to the user successfully.');
    }

    /**
     * Remove a role from a user.
     *
     * @param User $user
     * @return JsonResponse
     */
    public function removeRole(User $user,Role $role)
    {
        $user->removeRole($role);
        return self::success(null, 'The role for the user has been successfully deleted.');
    }

}
