<?php

namespace App\Http\Controllers\Permission;

use App\Http\Controllers\Controller;
use App\Http\Requests\Permission\StorePermissionRequest;
use App\Http\Requests\Permission\UpdatePermissionRequest;
use App\Http\Resources\PermissionResource;
use App\Services\Permission\PermissionService;
use Illuminate\Support\Facades\Cache;
use Spatie\Permission\Models\Permission;

class PermissionController extends Controller
{
    protected $permissionService;
    public function __construct(PermissionService $permissionService)
    {
        $this->permissionService = $permissionService;
    }

    /**
     * Get list of permissions
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $response = $this->permissionService->getAll();
        return self::success(PermissionResource::collection($response['permissions']));
    }

    /**
     * Create new permission in storage
     * @param \App\Http\Requests\Permission\StorePermissionRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(StorePermissionRequest $request)
    {
        $response = $this->permissionService->createNew($request->validated());
        return $response['status']
            ? self::success($response['permission'], 'Permission created successfully', 201)
            : self::error(null, $response['msg'], $response['code']);
    }

    /**
     * Get permission info
     * @param \Spatie\Permission\Models\Permission $permission
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $permission = Permission::find($id);
        if (!$permission) {
            return self::error('', 'Permission Not Found', 404);
        }
        return self::success(new PermissionResource($permission));
    }

    /**
     * Update permission info
     * @param \App\Http\Requests\Permission\UpdatePermissionRequest $request
     * @param mixed $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdatePermissionRequest $request, $id)
    {
        $response = $this->permissionService->change($request->validated(), $id);
        return $response['status']
            ? self::success(new PermissionResource($response['permission']), 'Permission Updated Successfully', 200)
            : self::error(new PermissionResource($response['permission']), $response['msg'], $response['code']);
    }

    /**
     * Delete permission from storage
     * @param mixed $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $permission = Permission::find($id);
        if (!$permission) {
            return self::error('', 'Permission not found', 404);
        }
        $permission->delete();

        return self::success('', 'Permission deleted successfully', 200);
    }
}
