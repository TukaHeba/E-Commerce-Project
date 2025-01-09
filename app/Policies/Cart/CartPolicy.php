<?php

namespace App\Policies\Cart;

use App\Models\Cart\Cart;
use App\Models\User\User;

/**
 * Class CartPolicy
 *
 * This policy defines the authorization rules for performing actions on a user's cart.
 * It ensures that only the appropriate users can access, update, or delete carts, 
 * as well as perform actions like checkout or placing an order.
 *
 * @package App\Policies\Cart
 */
class CartPolicy
{
    /**
     * Create a new policy instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Determine if the user can view the list of all carts.
     *
     * @param User $user The authenticated user.
     * @return bool True if the user has the 'admin' role.
     */
    public function index(User $user)
    {
        return $user->hasRole('admin');
    }

    /**
     * Determine if the user can store (create) a cart.
     *
     * @param User $user The authenticated user.
     * @param Cart $cart The cart being checked.
     * @return bool True if the user is the owner of the cart.
     */
    public function store(User $user, Cart $cart)
    {
        return $user->id == $cart->user_id;
    }

    /**
     * Determine if the user can view a specific cart.
     *
     * @param User $user The authenticated user.
     * @return bool True if the user has the 'admin' role.
     */
    public function show(User $user)
    {
        return $user->hasRole('admin');
    }

    /**
     * Determine if the user can update the cart.
     *
     * @param User $user The authenticated user.
     * @param Cart $cart The cart being checked.
     * @return bool True if the user is the owner of the cart.
     */
    public function update(User $user, Cart $cart)
    {
        return $user->id == $cart->user_id;
    }

    /**
     * Determine if the user can delete the cart.
     *
     * @param User $user The authenticated user.
     * @param Cart $cart The cart being checked.
     * @return bool True if the user is the owner of the cart.
     */
    public function delete(User $user, Cart $cart)
    {
        return $user->id == $cart->user_id;
    }

    /**
     * Determine if the user can view their own cart.
     *
     * @param User $user The authenticated user.
     * @param Cart $cart The cart being checked.
     * @return bool True if the user is the owner of the cart.
     */
    public function userCart(User $user, Cart $cart)
    {
        return $user->id == $cart->user_id;
    }

    /**
     * Determine if the user can perform checkout with their cart.
     *
     * @param User $user The authenticated user.
     * @param Cart $cart The cart being checked.
     * @return bool True if the user is the owner of the cart.
     */
    public function checkout(User $user, Cart $cart)
    {
        return $user->id == $cart->user_id;
    }

    /**
     * Determine if the user can place an order using their cart.
     *
     * @param User $user The authenticated user.
     * @param Cart $cart The cart being checked.
     * @return bool True if the user is the owner of the cart.
     */
    public function placeOrder(User $user, Cart $cart)
    {
        return $user->id == $cart->user_id;
    }
}
