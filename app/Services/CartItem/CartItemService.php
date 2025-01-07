<?php

namespace App\Services\CartItem;

use App\Models\CartItem\CartItem;

class CartItemService
{
    /**
     * store a new Item in cart and validate if the item already exists
     *
     * @param array $data
     * @throws \Exception
     * @return mixed
     */
    public function store(array $data)
    {
        $cart = auth()->user()->cart;
        $cartItem = $cart->cartItems()->firstOrCreate(['product_id' => $data['product_id']], $data);
        if (!$cartItem->wasRecentlyCreated) {
            throw new \Exception('The product is already in your cart.');
        }
        return $cartItem;
    }

    /**
     * delete item from cart.
     * @param CartItem $cartItem
     * @return void
     */
    public function deleteItem(CartItem $cartItem)
    {
        $cartItem->forceDelete();
    }
}
