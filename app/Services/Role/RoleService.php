<?php

namespace App\Services\Role;

use Exception;
use Illuminate\Support\Facades\Cache;
use Spatie\Permission\Models\Role;

class RoleService
{
    /**
     * Get list of roles
     * @return array
     */
    public function getAll()
    {
        $roles = Cache::remember("roles", 600, function () {
            return Role::all();
        });


        return [
            'status' => true,
            'roles' => $roles
        ];
    }

    /**
     * Store role in storage
     * @param array $data
     * @return array
     */
    public function createNew(array $data)
    {
        try {
            $role = Role::create($data);
            return [
                'status' => true,
                'role' => $role
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
     * Update role info
     * @param array $data
     * @param string $id
     * @return array
     */
    public function change(array $data, string $id)
    {
        $role = Role::find($id);
        if (!$role) {
            return [
                'status' => false,
                'role' => $role,
                'msg' => 'Role not found',
                'code' => 404
            ];
        }

        $filteredData = array_filter($data, function ($value) {
            return !is_null($value) && trim($value) !== '';
        });

        if (count($filteredData) < 1) {
            return [
                'status' => false,
                'role' => $role,
                'msg' => 'Not Found Any Data to Update',
                'code' => 400
            ];
        }

        $role->update($filteredData);
        return [
            'status' => true,
            'role' => $role
        ];
    }
}
