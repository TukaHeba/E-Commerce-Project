<?php

namespace App\Services\Permission;

use Illuminate\Support\Facades\Log;
use Illuminate\Database\QueryException;
use Spatie\Permission\Models\Permission;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class PermissionService
{
    /**
     * Retrieve all permissions with pagination.
     *
     * @return \Illuminate\Pagination\LengthAwarePaginator Paginated permissions.
     * 
     * @throws QueryException If retrieval fails.
     * @throws AuthorizationException If the user is not authorized to access permissions.
     * @throws AuthenticationException If the user is unauthenticated.
     */
    public function getPermissions()
    {
        try {
            return Permission::paginate(10);
        } catch (QueryException $e) {
            Log::error('Failed to retrieve permissions: ' . $e->getMessage());
            throw $e;
        } catch (AuthorizationException $e) {
            Log::error('Failed to retrieve permissions: ' . $e->getMessage());
            throw $e;
        } catch (AuthenticationException $e) {
            Log::error('Failed to retrieve permissions: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Create a new permission with the provided data.
     *
     * @param array $data The validated data to create a permission.
     * @return Permission|null The created permission object on success, or null on failure.
     * 
     * @throws QueryException If permission creation fails.
     * @throws AuthorizationException If the user is not authorized to create a permission.
     * @throws AuthenticationException If the user is unauthenticated.
     */
    public function storePermission(array $data)
    {
        try {
            $permission = Permission::create($data);
            return $permission;
        } catch (QueryException $e) {
            Log::error('Permission creation failed: ' . $e->getMessage());
            throw $e;
        } catch (AuthorizationException $e) {
            Log::error('Permission creation failed: ' . $e->getMessage());
            throw $e;
        } catch (AuthenticationException $e) {
            Log::error('Permission creation failed: ' . $e->getMessage());
            throw $e;
         }
    }

    /**
     * Display the specified permission along with its associated permissions.
     *
     * @param \Spatie\Permission\Models\Permission $permission The permission instance to display.
     * @return \Spatie\Permission\Models\Permission The permission instance with loaded permissions.
     * 
     * @throws ModelNotFoundException If the permission is not found.
     * @throws AuthorizationException If the user is not authorized to access the permission.
     * @throws AuthenticationException If the user is unauthenticated.
     * @throws QueryException If a database query error occurs.
     */
    public function showPermission(Permission $permission)
    {
        try {
            return $permission->load('roles');
        } catch (ModelNotFoundException $e) {
            Log::error('Failed to retrieve permission: ' . $e->getMessage());
            throw $e;
        } catch (AuthorizationException $e) {
            Log::error('Failed to retrieve permission: ' . $e->getMessage());
            throw $e;
        } catch (AuthenticationException $e) {
            Log::error('Failed to retrieve permission: ' . $e->getMessage());
            throw $e;
        } catch (QueryException $e) {
            Log::error('Failed to retrieve permission: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Update an existing permission with the provided data.
     *
     * @param Permission $permission The Permission to update.
     * @param array $data The validated data to update the permission.
     * @return Permission|null The updated permission object on success, or null on failure.
     * 
     * @throws ModelNotFoundException If the permission is not found.
     * @throws QueryException If the update fails.
     * @throws AuthorizationException If the user is not authorized to update the permission.
     * @throws AuthenticationException If the user is unauthenticated.
     */
    public function updatePermission(Permission $permission, array $data)
    {
        try {
            $permission->update(array_filter($data));
            return $permission;
        } catch (ModelNotFoundException $e) {
            Log::error('Permission update failed: ' . $e->getMessage());
            throw $e;
        } catch (QueryException $e) {
            Log::error('Permission update failed: ' . $e->getMessage());
            throw $e;
        } catch (AuthorizationException $e) {
            Log::error('Failed to retrieve permissions: ' . $e->getMessage());
            throw $e;
        } catch (AuthenticationException $e) {
            Log::error('Failed to retrieve permissions: ' . $e->getMessage());
            throw $e;
         }
    }
}
