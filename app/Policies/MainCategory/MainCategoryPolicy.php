<?php

namespace App\Policies\MainCategory;

use App\Models\User\User;

/**
 * Class MainCategoryPolicy
 *
 * This policy defines the authorization rules for actions on the MainCategory model.
 * It restricts access to specific actions based on the roles of the authenticated user.
 *
 * @package App\Policies\MainCategory
 */
class MainCategoryPolicy
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
     * Determine if the user can create a new MainCategory.
     *
     * @param User $user The authenticated user.
     * @return bool True if the user has the role of 'admin' or 'store manager'; otherwise, false.
     */
    public function store(User $user)
    {
        return  $user->hasRole(['admin', 'store manager']);
    }

    /**
     * Determine if the user can update an existing MainCategory.
     *
     * @param User $user The authenticated user.
     * @return bool True if the user has the role of 'admin' or 'store manager'; otherwise, false.
     */
    public function update(User $user)
    {
        return  $user->hasRole(['admin', 'store manager']);
    }

    /**
     * Determine if the user can delete a MainCategory.
     *
     * @param User $user The authenticated user.
     * @return bool True if the user has the role of 'admin' or 'store manager'; otherwise, false.
     */
    public function delete(User $user)
    {
        return  $user->hasRole(['admin', 'store manager']);
    }

    /**
     * Determine if the user can view deleted MainCategories.
     *
     * @param User $user The authenticated user.
     * @return bool True if the user has the role of 'admin' or 'store manager'; otherwise, false.
     */
    public function showDeleted(User $user)
    {
        return  $user->hasRole(['admin', 'store manager']);
    }

     /**
     * Determine if the user can restore deleted MainCategories.
     *
     * @param User $user The authenticated user.
     * @return bool True if the user has the role of 'admin' or 'store manager'; otherwise, false.
     */
    public function restoreDeleted(User $user)
    {
        return  $user->hasRole(['admin', 'store manager']);
    }

    /**
     * Determine if the user can permanently delete a MainCategory.
     *
     * @param User $user The authenticated user.
     * @return bool True if the user has the role of 'admin'; otherwise, false.
     */
    public function forceDeleted(User $user)
    {
        return $user->hasRole(roles: 'admin');
    }
}
