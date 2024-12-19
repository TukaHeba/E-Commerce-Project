<?php

namespace App\Services\Role;

use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\QueryException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class RoleService
{
    /**
     * Retrieve all roles with pagination.
     *
     * @return \Illuminate\Pagination\LengthAwarePaginator Paginated roles.
     * 
     * @throws QueryException If retrieval fails.
     * @throws AuthorizationException If the user is not authorized to access roles.
     * @throws AuthenticationException If the user is unauthenticated.
     */
    public function getRoles()
    {
        try {
            return Role::paginate(10);
        } catch (QueryException $e) {
            Log::error('Failed to retrieve roles: ' . $e->getMessage());
            throw $e;
        } catch (AuthorizationException $e) {
            Log::error('Failed to retrieve roles: ' . $e->getMessage());
            throw $e;
        } catch (AuthenticationException $e) {
            Log::error('Failed to retrieve roles: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Create a new role with the provided data.
     *
     * @param array $data The validated data to create a role.
     * @return Role|null The created role object on success, or null on failure.
     * 
     * @throws QueryException If role creation fails.
     * @throws AuthorizationException If the user is not authorized to create a role.
     * @throws AuthenticationException If the user is unauthenticated.
     */
    public function storeRole(array $data)
    {
        try {
            $role = Role::create($data);
            return $role;
        } catch (QueryException $e) {
            Log::error('Role creation failed: ' . $e->getMessage());
            throw $e;
        } catch (AuthorizationException $e) {
            Log::error('Role creation failed: ' . $e->getMessage());
            throw $e;
        } catch (AuthenticationException $e) {
            Log::error('Role creation failed: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Display the specified role along with its associated permissions.
     *
     * @param \Spatie\Permission\Models\Role $role The role instance to display.
     * @return \Spatie\Permission\Models\Role The role instance with loaded permissions.
     * 
     * @throws ModelNotFoundException If the role is not found.
     * @throws AuthorizationException If the user is not authorized to access the role.
     * @throws AuthenticationException If the user is unauthenticated.
     * @throws QueryException If a database query error occurs.
     */
    public function showRole(Role $role)
    {
        try {
            return $role->load('permissions');
        } catch (ModelNotFoundException $e) {
            Log::error('Failed to retrieve role: ' . $e->getMessage());
            throw $e;
        } catch (AuthorizationException $e) {
            Log::error('Failed to retrieve role: ' . $e->getMessage());
            throw $e;
        } catch (AuthenticationException $e) {
            Log::error('Failed to retrieve role: ' . $e->getMessage());
            throw $e;
        } catch (QueryException $e) {
            Log::error('Failed to retrieve role: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Update an existing role with the provided data.
     *
     * @param Role $role The Role to update.
     * @param array $data The validated data to update the role.
     * @return Role|null The updated role object on success, or null on failure.
     * 
     * @throws ModelNotFoundException If the role is not found.
     * @throws QueryException If the update fails.
     * @throws AuthorizationException If the user is not authorized to update the role.
     * @throws AuthenticationException If the user is unauthenticated.
     */
    public function updateRole(Role $role, array $data)
    {
        try {
            $role->update(array_filter($data));
            return $role;
        } catch (ModelNotFoundException $e) {
            Log::error('Role update failed: ' . $e->getMessage());
            throw $e;
        } catch (QueryException $e) {
            Log::error('Role update failed: ' . $e->getMessage());
            throw $e;
        } catch (AuthorizationException $e) {
            Log::error('Failed to retrieve roles: ' . $e->getMessage());
            throw $e;
        } catch (AuthenticationException $e) {
            Log::error('Failed to retrieve roles: ' . $e->getMessage());
            throw $e;
        }
    }
}
