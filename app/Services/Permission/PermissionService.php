<?php

namespace App\Services\Permission;

use Spatie\Permission\Models\Permission;

class PermissionService
{
    /**
     * Retrieve all permissions with pagination.
     *
     * @return \Illuminate\Pagination\LengthAwarePaginator Paginated permissions.
     */
    public function getPermissions()
    {
        return Permission::paginate(10);
    }

    /**
     * Create a new permission with the provided data.
     *
     * @param array $data The validated data to create a permission.
     * @return Permission|null The created permission object on success, or null on failure.
     */
    public function storePermission(array $data)
    {
        $permission = Permission::create($data);
        return $permission;
    }

    /**
     * Display the specified permission along with its associated permissions.
     *
     * @param \Spatie\Permission\Models\Permission $permission The permission instance to display.
     * @return \Spatie\Permission\Models\Permission The permission instance with loaded permissions.
     */
    public function showPermission(Permission $permission)
    {
        return $permission->load('roles');
    }

    /**
     * Update an existing permission with the provided data.
     *
     * @param Permission $permission The Permission to update.
     * @param array $data The validated data to update the permission.
     * @return Permission|null The updated permission object on success, or null on failure.
     */
    public function updatePermission(Permission $permission, array $data)
    {
        $permission->update(array_filter($data));
        return $permission;
    }
}
