<?php
namespace App\Policies\User;

use App\Models\User\User;

/**
 * Class UserPolicy
 *
 * This policy defines the authorization rules for actions on the User model.
 * It enforces role-based permissions and ensures that users can only manage their own accounts unless they have administrative privileges.
 *
 * @package App\Policies\UserPolicy
 */
class UserPolicy
{
    /**
     * Create a new policy instance.
     *
     * The constructor initializes the policy instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Determine if the user can view a list of users.
     *
     * @param User $user The authenticated user.
     * @return bool True if the user has the 'admin' role; otherwise, false.
     */
    public function index(User $user)
    {
        return $user->hasRole(roles: 'admin');
    }

    /**
     * Determine if the user can create a new user.
     *
     * @param User $user The authenticated user.
     * @return bool True if the user has the 'admin' role; otherwise, false.
     */
    public function store(User $user)
    {
        return $user->hasRole(roles: 'admin');
    }

    /**
     * Determine if the user can view a specific user.
     *
     * @param User $user The authenticated user.
     * @param User $model The user being viewed.
     * @return bool True if the authenticated user is viewing their own account or has the 'admin' role; otherwise, false.
     */
    public function show(User $user, User $model)
    {
        return $user->id === $model->id || $user->hasRole('admin');
    }

    /**
     * Determine if the user can update a specific user.
     *
     * @param User $user The authenticated user.
     * @param User $model The user being updated.
     * @return bool True if the authenticated user is updating their own account or has the 'admin' role; otherwise, false.
     */
    public function update(User $user, User $model)
    {
        return $user->id === $model->id || $user->hasRole('admin');
    }

    /**
     * Determine if the user can delete a specific user.
     *
     * @param User $user The authenticated user.
     * @param User $model The user being deleted.
     * @return bool True if the authenticated user is deleting their own account or has the 'admin' role; otherwise, false.
     */
    public function delete(User $user, User $model)
    {
        return $user->id === $model->id || $user->hasRole('admin');
    }

    /**
     * Determine if the user can view deleted users.
     *
     * @param User $user The authenticated user.
     * @return bool True if the user has the 'admin' role; otherwise, false.
     */
    public function showDeleted(User $user)
    {
        return $user->hasRole(roles: 'admin');
    }

    /**
     * Determine if the user can permanently delete a user.
     *
     * @param User $user The authenticated user.
     * @return bool True if the user has the 'admin' role; otherwise, false.
     */
    public function forceDeleted(User $user)
    {
        return $user->hasRole(roles: 'admin');
    }
}
