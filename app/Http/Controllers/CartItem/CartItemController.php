<?php

namespace App\Http\Controllers\CartItem;

use App\Http\Controllers\Controller;
use App\Http\Requests\CartItem\StoreCartItemRequest;
use App\Http\Requests\CartItem\UpdateCartItemRequest;
use App\Http\Resources\CartItemResource;
use App\Models\CartItem\CartItem;
use App\Services\CartItem\CartItemService;

class CartItemController extends Controller
{

    protected CartItemService $cartItemService;

    public function __construct(CartItemService $cartItemService)
    {
        $this->cartItemService = $cartItemService;
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param StoreCartItemRequest $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */

    public function store(StoreCartItemRequest $request)
    {
        $data = $request->validationData();
        $this->cartItemService->store($data);
        return self::success(null, 'Added successfully!', 201);
    }


    /**
     *  Update the specified resource in storage.
     *
     * @param UpdateCartItemRequest $request
     * @param CartItem $cartItem
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateCartItemRequest $request, CartItem $cartItem)
    {
        $cartItem->update(['quantity' => $request->quantity]);
        return self::success(new CartItemResource($cartItem), 'updated successfully!');
    }

    /**
     *  Remove the specified resource from storage.
     *
     * @param CartItem $cartItem
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(CartItem $cartItem)
    {
        $this->cartItemService->deleteItem($cartItem);
        return self::success(null, 'deleted successfully!');
    }


}
