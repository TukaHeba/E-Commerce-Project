<?php

namespace App\Services\Permission;

use Exception;
use Illuminate\Support\Facades\Cache;
use Spatie\Permission\Models\Permission;

class PermissionService
{
    /**
     * Get list of permissions
     * @return array
     */
    public function getAll()
    {
        $permissions = Cache::remember("permissions", 600, function () {
            return Permission::all();
        });


        return [
            'status' => true,
            'permissions' => $permissions
        ];
    }

    /**
     * Store permission in storage
     * @param array $data
     * @return array
     */
    public function createNew(array $data)
    {
        try {
            $permission = Permission::create($data);
            return [
                'status' => true,
                'permission' => $permission
            ];
        } catch (Exception $e) {
            return [
                'status' => false,
                'msg' => $e->getMessage(),
                'code' => 500
            ];
        }

    }

    /**
     * Update permission info
     * @param array $data
     * @param string $id
     * @return array
     */
    public function change(array $data, string $id)
    {
        $permission = Permission::find($id);
        if (!$permission) {
            return [
                'status' => false,
                'permission' => $permission,
                'msg' => 'Permission not found',
                'code' => 404
            ];
        }

        $filteredData = array_filter($data, function ($value) {
            return !is_null($value) && trim($value) !== '';
        });

        if (count($filteredData) < 1) {
            return [
                'status' => false,
                'permission' => $permission,
                'msg' => 'Not Found Any Data to Update',
                'code' => 400
            ];
        }

        $permission->update($filteredData);
        return [
            'status' => true,
            'permission' => $permission
        ];
    }
}
