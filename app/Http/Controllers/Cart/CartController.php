<?php

namespace App\Http\Controllers\Cart;

use App\Http\Controllers\Controller;
use App\Http\Requests\Cart\StoreCartRequest;
use App\Http\Requests\Cart\UpdateCartRequest;
use App\Http\Requests\Order\StoreOrderRequest;
use App\Http\Resources\CartResource;
use App\Models\Cart\Cart;
use App\Services\Cart\CartService;
use Illuminate\Http\JsonResponse;

class CartController extends Controller
{

    protected CartService $CartService;

    public function __construct(CartService $CartService)
    {
        $this->CartService = $CartService;
    }

    /**
     * View all user carts for ((Admin))
     *
     * //     * @return \Illuminate\Http\JsonResponse
     */

    public function index()
    {
        $carts = Cart::paginate(10);
        return self::paginated($carts, CartResource::class, 'Carts retrieved successfully', 200);
    }

    /**
     *   Display the specified cart for ((Admin))
     *
     * @param Cart $cartItem
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Cart $cart)
    {
        return self::success(new CartResource($cart->load(['user', 'cartItems.product'])));
    }


    /**
     *  View the cart of the auth user.
     * @return \Illuminate\Http\JsonResponse
     */
    public function userCart()
    {
        $cart = Cart::where('user_id', auth()->user()->id)->with('cartItems.product')->first();
        return self::success(new CartResource($cart));
    }


    /**
     * Checkout the cart and get cart items data and total price.
     *
     * @return JsonResponse
     */
    public function checkout()
    {
        $cartData = $this->CartService->cartCheckout();
        return self::success([
            'cart_items' => $cartData['cart_items'],
            'total_price' => $cartData['total_price'],
        ]);
    }

    /**
     * Place an order by creating the order and order items, then clearing the cart.
     *
     * @param \App\Http\Requests\Order\StoreOrderRequest $request
     * @return JsonResponse
     */
    public function placeOrder(StoreOrderRequest $request)
    {
        $validated = $request->validated();
        $order = $this->CartService->placeOrder($validated['shipping_address']);

        return self::success($order, 'Order placed successfully!', 201);
    }
}
