<?php
namespace App\Policies\UserPolicy;

use App\Models\User\User;

class UserPolicy
{
    /**
     * Create a new policy instance.
     */
    public function __construct()
    {
        //
    }

    public function index(User $user)
    {
        return $user->hasRole(roles: 'admin');
    }

    public function store(User $user)
    {
        return $user->hasRole(roles: 'admin');
    }

    public function show(User $user, User $model)
    {
        return $user->id === $model->id || $user->hasRole('admin');
    }

    public function update(User $user, User $model)
    {
        return $user->id === $model->id || $user->hasRole('admin');
    }

    public function delete(User $user, User $model)
    {
        return $user->id === $model->id || $user->hasRole('admin');
    }

    public function showDeleted(User $user)
    {
        return $user->hasRole(roles: 'admin');
    }

    public function forceDeleted(User $user)
    {
        return $user->hasRole(roles: 'admin');
    }
}
