<?php

namespace App\Http\Controllers\Cart;

use App\Models\Cart\Cart;
use Illuminate\Http\JsonResponse;
use App\Services\Cart\CartService;
use App\Http\Controllers\Controller;
use App\Http\Resources\CartResource;
use App\Http\Resources\OrderResource;
use App\Http\Requests\Order\StoreOrderRequest;

class CartController extends Controller
{
    protected CartService $CartService;

    public function __construct(CartService $CartService)
    {
        $this->CartService = $CartService;
    }

    /**
     * Retrieve all carts (Admin only).
     *
     * @return JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */

    public function index()
    {
        $this->authorize('index', Cart::class);
        $carts = Cart::with('user')->paginate(10);
        return self::paginated($carts, CartResource::class, 'Carts retrieved successfully', 200);
    }

    /**
     * Retrieve a specific cart (Admin only).
     *
     * @param Cart $cart
     * @return JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function show(Cart $cart)
    {
        $this->authorize('show', Cart::class);
        return self::success(new CartResource($cart->load(['user', 'cartItems.product'])));
    }

    /**
     * Retrieve the authenticated user's cart.
     *
     * @return JsonResponse
     */
    public function userCart()
    {
        $cart = Cart::where('user_id', auth()->user()->id)->with('cartItems.product')->firstOrFail();
        return self::success(new CartResource($cart));
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
