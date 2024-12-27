<?php

namespace App\Http\Controllers\CartItem;

use App\Http\Controllers\Controller;
use App\Http\Requests\CartItem\StoreCartItemRequest;
use App\Http\Requests\CartItem\UpdateCartItemRequest;
use App\Models\Cart\Cart;
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
     * View all user carts for ((Admin))
     *
     * @return \Illuminate\Http\JsonResponse
     */

    public function index()
    {
        $data = Cart::with('cartItems.product')->get();
        return self::success($data);

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
     *   Display the specified cart for ((Admin))
     *
     * @param Cart $cartItem
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Cart $cartItem)
    {
        return self::success($cartItem->load('cartItems.product'));
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
        return self::success($cartItem, 'updated successfully!');
    }

    /**
     *  Remove the specified resource from storage.
     *
     * @param CartItem $cartItem
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(CartItem $cartItem)
    {
        $cartItem->forceDelete();
        return self::success(null, 'deleted successfully!');
    }

    /**
     *  View the cart of the auth user.
     * @return \Illuminate\Http\JsonResponse
     */
    public function userCart()
    {
        $data = Cart::where('user_id', auth()->user()->id)->with('cartItems.product')->first();
        return self::success($data);
    }

}
