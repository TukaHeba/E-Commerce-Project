<?php

namespace App\Http\Controllers\Cart;

use App\Http\Controllers\Controller;
use App\Http\Requests\Order\StoreOrderRequest;
use App\Http\Resources\CartResource;
use App\Http\Resources\OrderResource;
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
     * @return \Illuminate\Http\JsonResponse
     */

    public function index()
    {
        $this->authorize('index', Cart::class);
        $carts = Cart::paginate(10);
        return self::paginated($carts, CartResource::class, 'Carts retrieved successfully', 200);
    }

    /**
     * Display the specified cart for ((Admin))
     *
     * @param Cart $cartItem
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Cart $cart)
    {
        $this->authorize('show', Cart::class);
        return self::success(new CartResource($cart->load(['user', 'cartItems.product'])));
    }

    /**
     * View the cart of the auth user.
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function userCart()
    {
        $cart = Cart::where('user_id', auth()->user()->id)->with('cartItems.product')->first();
        return self::success(new CartResource($cart));
    }

    /**
     * Checkout the cart and get cart items data with total price.
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
     * Place an order by creating the order and order items.
     *
     * @param \App\Http\Requests\Order\StoreOrderRequest $request
     * @return JsonResponse
     */
    public function placeOrder(StoreOrderRequest $request)
    {
        $order = $this->CartService->placeOrder($request->validated());
        return self::success(new OrderResource($order), 'Order placed successfully!', 201);
    }
}
