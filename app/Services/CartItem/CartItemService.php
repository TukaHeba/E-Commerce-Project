<?php

namespace App\Services\CartItem;

use App\Models\Cart\Cart;

class CartItemService
{

    public function store(array $data){
        $cart = Cart::query()->firstOrCreate(['user_id'=>auth()->user()->id]);

        if($cart->cartItems()->where('product_id',$data['product_id'])->first()){
            throw new \Exception('The product is already in your cart.');
        }
        $cart->cartItems()->create($data);

    }
}
