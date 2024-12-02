<?php

namespace App\Http\Controllers\Role;

use App\Http\Controllers\Controller;
use App\Http\Requests\Role\StoreRoleRequest;
use App\Http\Requests\Role\UpdateRoleRequest;
use App\Http\Resources\RoleResource;
use Illuminate\Support\Facades\Cache;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    /**
     * Get list of roles
     * @return mixed|\Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $roles = Cache::remember("roles", 600, function () {
            return Role::all();
        });

        return self::success(RoleResource::collection($roles));
    }

    /**
     * Create new role in storage
     * @param \App\Http\Requests\Role\StoreRoleRequest $request
     * @return mixed|\Illuminate\Http\JsonResponse
     */
    public function store(StoreRoleRequest $request)
    {
        $role = Role::create($request->validated());

        return self::success($role, 'Role created successfully', 201);
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
        $role = Role::find($id);
        if (!$role) {
            return self::error('', 'Role not found', 404);
        }

        $filteredData = array_filter($request->validated(), function ($value) {
            return !is_null($value) && trim($value) !== '';
        });

        if (count($filteredData) < 1) {
            return self::error($filteredData, 'Not Found Any Data to Update', 404);
        }

        $role->update($filteredData);

        return self::success(new RoleResource($role), 'Role updated successfully');
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
