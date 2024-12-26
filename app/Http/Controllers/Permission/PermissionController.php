<?php

namespace App\Http\Controllers\Permission;

use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Spatie\Permission\Models\Permission;
use App\Http\Resources\PermissionResource;
use App\Services\Permission\PermissionService;
use App\Http\Requests\Permission\StorePermissionRequest;
use App\Http\Requests\Permission\UpdatePermissionRequest;

class PermissionController extends Controller
{
    protected $PermissionService;
    public function __construct(PermissionService $PermissionService)
    {
        $this->PermissionService = $PermissionService;
    }

    /**
     * Display a listing of the resource.
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(): JsonResponse
    {
        $permissions = $this->PermissionService->getPermissions();
        return self::paginated($permissions, PermissionResource::class, 'Permissions retrieved successfully', 200);
    }

    /**
     * Store a newly created resource in storage.
     * 
     * @param \App\Http\Requests\Permission\StorePermissionRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(StorePermissionRequest $request): JsonResponse
    {
        $permission = $this->PermissionService->storePermission($request->validated());
        return self::success(new PermissionResource($permission), 'Permission created successfully', 201);
    }

    /**
     * Display the specified resource.
     * 
     * @param \Spatie\Permission\Models\Permission $permission
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Permission $permission): JsonResponse
    {
        $permissionData = $this->PermissionService->showPermission($permission);
        return self::success(new PermissionResource($permissionData), 'Permission retrieved successfully', 200);
    }

    /**
     * Update the specified resource in storage.
     * 
     * @param \App\Http\Requests\Permission\UpdatePermissionRequest $request
     * @param \Spatie\Permission\Models\Permission $permission
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdatePermissionRequest $request, Permission $permission): JsonResponse
    {
        $updatedPermission = $this->PermissionService->updatePermission($permission, $request->validated());
        return self::success(new PermissionResource($updatedPermission), 'Permission updated successfully', 200);
    }

    /**
     * Remove the specified resource from storage.
     * 
     * @param \Spatie\Permission\Models\Permission $permission
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Permission $permission): JsonResponse
    {
        $permission->delete();
        return self::success(null, 'Permission deleted successfully', 200);
    }
}
