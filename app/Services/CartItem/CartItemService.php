<?php

namespace App\Services\CartItem;

use App\Models\Cart\Cart;
use App\Models\CartItem\CartItem;

class CartItemService
{

    /**
     * create cart when doesn't exists and add items in to cart
     *
     * @param array $data
     * @return void
     * @throws \Exception
     */
    public function store(array $data)
    {
        $cart = Cart::where('user_id' , auth()->user()->id)->first();
        if ($cart->cartItems()->where('product_id', $data['product_id'])->first()) {
            throw new \Exception('The product is already in your cart.');
        }
        $cart->cartItems()->create($data);
    }


    /**
     * delete items from cart
     * @param CartItem $cartItem
     * @return void
     */
    public function deleteItem(CartItem $cartItem)
    {
        $cartItem->forceDelete();
    }
}
