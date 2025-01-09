<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\StoreUserRequest;
use App\Http\Requests\User\UpdateUserRequest;
use App\Http\Resources\OrderResource;
use App\Http\Resources\UserResource;
use App\Models\User\User;
use App\Services\User\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;


class UserController extends Controller
{

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
    public function index(Request $request): JsonResponse
    {
        $users = $this->UserService->getUsers($request);
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
        $users = User::onlyTrashed()->get();
     return self::success(UserResource::collection($users), 'Users retrieved successfully');

    }
     /**
     * Restore a soft-deleted user.
     *
     * @param string $id The ID of the user to restore.
     * @return \Illuminate\Http\JsonResponse
     */
    public function restoreDeleted($id): JsonResponse
    {
        $user = User::onlyTrashed()->findOrFail($id);
        $user->restore();
        return self::success( null , 'User restored successfully');
    }
     /**
     * Permanently delete a soft-deleted user.
     *
     * @param  string $id The ID of the user the user to permanently delete.
     * @return \Illuminate\Http\JsonResponse
     */
    public function forceDeleted($id): JsonResponse
    {
        $user = User::onlyTrashed()->findOrFail($id)->forceDelete();
        return self::success(null, 'User force deleted successfully');
    }

     /**
     * Calculate the average total price of all delivered orders for the user.
     *
     * @param string $id The ID of the user.
     * @return \Illuminate\Http\JsonResponse
     */

     public function userPurchasesAverage($user)
     {
         $userPurchasesAverage = $this->UserService->userPurchasesAverage($user);
         return self::success($userPurchasesAverage, 'the average total price of all delivered orders for the user');
     }

}
