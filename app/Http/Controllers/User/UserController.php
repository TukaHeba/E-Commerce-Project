<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\StoreUserRequest;
use App\Http\Requests\User\UpdateUserRequest;
use App\Http\Resources\OrderResource;
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
     * Display a listing of the resource.
     * @throws \Exception
     */
    public function index(Request $request): JsonResponse
    {
        $users = $this->UserService->getUsers($request);
        return self::success($users, 'Users retrieved successfully', 200);
    }

    /**
     * Store a newly created resource in storage.
     * @throws \Exception
     */
    public function store(StoreUserRequest $request): JsonResponse
    {
        $user = $this->UserService->storeUser($request->validated());
        return self::success($user, 'User created successfully', 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user): JsonResponse
    {
        return self::success($user, 'User retrieved successfully');
    }

    /**
     * Update the specified resource in storage.
     * @throws \Exception
     */
    public function update(UpdateUserRequest $request, User $user): JsonResponse
    {
        $updatedUser = $this->UserService->updateUser($user, $request->validated());
        return self::success($updatedUser, 'User updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user): JsonResponse
    {
        $user->delete();
        return self::success(null, 'User deleted successfully');
    }

    /**
     * Display soft-deleted records.
     */
    public function showDeleted(): JsonResponse
    {
        $users = User::onlyTrashed()->get();
        return self::success($users, 'Users retrieved successfully');
    }

    /**
     * Restore a soft-deleted record.
     * @param string $id
     * @return JsonResponse
     */
    public function restoreDeleted(string $id): JsonResponse
    {
        $user = User::onlyTrashed()->findOrFail($id);
        $user->restore();
        return self::success($user, 'User restored successfully');
    }

    /**
     * Permanently delete a soft-deleted record.
     * @param string $id
     * @return JsonResponse
     */
    public function forceDeleted(string $id): JsonResponse
    {
        $user = User::onlyTrashed()->findOrFail($id)->forceDelete();
        return self::success(null, 'User force deleted successfully');
    }

}
