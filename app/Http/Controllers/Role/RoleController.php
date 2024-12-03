<?php

namespace App\Http\Controllers\Role;

use App\Http\Controllers\Controller;
use App\Http\Requests\Role\StoreRoleRequest;
use App\Http\Requests\Role\UpdateRoleRequest;
use App\Http\Resources\RoleResource;
use App\Services\Role\RoleService;
use Illuminate\Support\Facades\Cache;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    protected $roleService;
    public function __construct(RoleService $roleService)
    {
        $this->roleService = $roleService;
    }

    /**
     * Get list of roles
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $response = $this->roleService->getAll();
        return self::success(RoleResource::collection($response['roles']));
    }

    /**
     * Create new role in storage
     * @param \App\Http\Requests\Role\StoreRoleRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(StoreRoleRequest $request)
    {
        $response = $this->roleService->createNew($request->validated());
        return $response['status']
            ? self::success($response['role'], 'Role created successfully', 201)
            : self::error(null, $response['msg'], $response['code']);
    }

    /**
     * Get role info
     * @param \Spatie\Permission\Models\Role $role
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $role = Role::find($id);
        if (!$role) {
            return self::error('', 'Role Not Found', 404);
        }
        return self::success(new RoleResource($role));
    }

    /**
     * Update role info
     * @param \App\Http\Requests\Role\UpdateRoleRequest $request
     * @param mixed $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateRoleRequest $request, $id)
    {
        $response = $this->roleService->change($request->validated(), $id);
        return $response['status']
            ? self::success(new RoleResource($response['role']), 'Role Updated Successfully', 200)
            : self::error(new RoleResource($response['role']), $response['msg'], $response['code']);
    }

    /**
     * Delete role from storage
     * @param mixed $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $role = Role::without('users')->find($id); // تجاهل العلاقة أثناء الجلب
        if (!$role) {
            return self::error('', 'Role not found', 404);
        }
        $role->delete();

        return self::success('', 'Role deleted successfully', 200);
    }
}
