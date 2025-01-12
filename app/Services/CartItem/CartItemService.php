<?php

namespace App\Services\CartItem;

class CartItemService
{
    /**
     * Add a new item to the cart and ensure it is not already present.
     *
     * @param array $data
     * @return void
     * @throws \Exception
     */
    public function store(array $data)
    {
        $cart = auth()->user()->cart;
        $cartItem = $cart->cartItems()->firstOrCreate(['product_id' => $data['product_id']], $data);
        if (!$cartItem->wasRecentlyCreated) {
            throw new \Exception('The product is already in your cart.');
        }
    }
}
