<?php

namespace App\Http\Controllers\Role;

use Illuminate\Http\JsonResponse;
use App\Services\Role\RoleService;
use Spatie\Permission\Models\Role;
use App\Http\Controllers\Controller;
use App\Http\Resources\RoleResource;
use App\Http\Requests\Role\StoreRoleRequest;
use App\Http\Requests\Role\UpdateRoleRequest;

class RoleController extends Controller
{
    protected RoleService $RoleService;
    public function __construct(RoleService $RoleService)
    {
        $this->RoleService = $RoleService;
    }

    /**
     * Display a listing of the resource.
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(): JsonResponse
    {
        $roles = $this->RoleService->getRoles();
        return self::paginated($roles, RoleResource::class, 'Roles retrieved successfully', 200);
    }

    /**
     * Store a newly created resource in storage.
     * 
     * @param \App\Http\Requests\Role\StoreRoleRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(StoreRoleRequest $request): JsonResponse
    {
        $role = $this->RoleService->storeRole($request->validated());
        return self::success(new RoleResource($role), 'Role created successfully', 201);
    }

    /**
     * Display the specified resource.
     * 
     * @param \Spatie\Permission\Models\Role $role
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Role $role): JsonResponse
    {
        $roleData = $this->RoleService->showRole($role);
        return self::success(new RoleResource($roleData), 'Role retrieved successfully', 200);
    }

    /**
     * Update the specified resource in storage.
     * 
     * @param \App\Http\Requests\Role\UpdateRoleRequest $request
     * @param \Spatie\Permission\Models\Role $role
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateRoleRequest $request, Role $role): JsonResponse
    {
        $updatedRole = $this->RoleService->updateRole($role, $request->validated());
        return self::success(new RoleResource($updatedRole), 'Role updated successfully', 200);
    }

    /**
     * Remove the specified resource from storage.
     * 
     * @param \Spatie\Permission\Models\Role $role
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Role $role): JsonResponse
    {
        $role->delete();
        return self::success(null, 'Role deleted successfully', 200);
    }
}
