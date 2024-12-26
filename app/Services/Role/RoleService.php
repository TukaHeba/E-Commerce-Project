<?php

namespace App\Services\Role;

use Spatie\Permission\Models\Role;

class RoleService
{
    /**
     * Retrieve all roles with pagination.
     *
     * @return \Illuminate\Pagination\LengthAwarePaginator Paginated roles.
     */
    public function getRoles()
    {
        return Role::paginate(10);
    }

    /**
     * Create a new role with the provided data.
     *
     * @param array $data The validated data to create a role.
     * @return Role|null The created role object on success, or null on failure.
     */
    public function storeRole(array $data)
    {
        $role = Role::create($data);
        return $role;
    }

    /**
     * Display the specified role along with its associated permissions.
     *
     * @param \Spatie\Permission\Models\Role $role The role instance to display.
     * @return \Spatie\Permission\Models\Role The role instance with loaded permissions.
     */
    public function showRole(Role $role)
    {
        return $role->load('permissions');
    }

    /**
     * Update an existing role with the provided data.
     *
     * @param Role $role The Role to update.
     * @param array $data The validated data to update the role.
     * @return Role|null The updated role object on success, or null on failure.
     */
    public function updateRole(Role $role, array $data)
    {
        $role->update(array_filter($data));
        return $role;
    }
}
