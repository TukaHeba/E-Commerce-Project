<?php

namespace App\Http\Controllers\CartItem;

use App\Models\CartItem\CartItem;
use App\Http\Controllers\Controller;
use App\Http\Resources\CartItemResource;
use App\Services\CartItem\CartItemService;
use App\Http\Requests\CartItem\StoreCartItemRequest;
use App\Http\Requests\CartItem\UpdateCartItemRequest;

class CartItemController extends Controller
{
    protected CartItemService $cartItemService;
    public function __construct(CartItemService $cartItemService)
    {
        $this->cartItemService = $cartItemService;
    }

    /**
     * Add a new item to the cart.
     *
     * @param StoreCartItemRequest $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function store(StoreCartItemRequest $request)
    {
        $data = $request->validationData();
        $this->cartItemService->store($data);
        return self::success(null,'A new item Added successfully!', 201);
    }

    /**
     * Update the quantity of an item in the cart.
     *
     * @param UpdateCartItemRequest $request
     * @param CartItem $cartItem
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateCartItemRequest $request, CartItem $cartItem)
    {
        $cartItem->update(['quantity' => $request->quantity]);
        return self::success(new CartItemResource($cartItem->load('product')), 'Item cart updated successfully!');
    }

    /**
     * Remove an item from the cart.
     *
     * @param CartItem $cartItem
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(CartItem $cartItem)
    {
        $cartItem->delete();
        return self::success(null, 'Item has been deleted successfully!');
    }
}
