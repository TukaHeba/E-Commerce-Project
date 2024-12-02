<?php

namespace App\Http\Controllers\Permission;

use App\Http\Controllers\Controller;
use App\Http\Requests\Permission\StorePermissionRequest;
use App\Http\Requests\Permission\UpdatePermissionRequest;
use App\Http\Resources\PermissionResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Spatie\Permission\Models\Permission;

class PermissionController extends Controller
{
    /**
     * Get list of permissions
     * @return mixed|\Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $permissions = Cache::remember("permissions", 600, function () {
            return Permission::all();
        });

        return self::success(PermissionResource::collection($permissions));
    }

    /**
     * Create new permission in storage
     * @param \App\Http\Requests\Permission\StorePermissionRequest $request
     * @return mixed|\Illuminate\Http\JsonResponse
     */
    public function store(StorePermissionRequest $request)
    {
        $permission = Permission::create($request->validated());

        return self::success($permission, 'Permission created successfully', 201);
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
        $permission = Permission::find($id);
        if (!$permission) {
            return self::error('', 'Permission not found', 404);
        }

        $filteredData = array_filter($request->validated(), function ($value) {
            return !is_null($value) && trim($value) !== '';
        });

        if (count($filteredData) < 1) {
            return self::error($filteredData, 'Not Found Any Data to Update', 404);
        }

        $permission->update($filteredData);

        return self::success(new PermissionResource($permission), 'Permission updated successfully');
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
