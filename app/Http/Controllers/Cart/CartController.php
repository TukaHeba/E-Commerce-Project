<?php

namespace App\Http\Controllers\Cart;

use App\Models\Cart\Cart;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Cart\StoreCartRequest;
use App\Http\Requests\Cart\UpdateCartRequest;

class CartController extends Controller
{
  
    protected CartService $CartService;

    public function __construct(CartService $CartService)
    {
        $this->CartService = $CartService;
    }

    /**
     * Display a listing of the resource.
     * @throws \Exception
     */
    public function index(Request $request): JsonResponse
    {
        $carts = $this->CartService->getCarts($request);
        return self::paginated($carts, 'Carts retrieved successfully', 200);
    }

    /**
     * Store a newly created resource in storage.
     * @throws \Exception
     */
    public function store(StoreCartRequest $request): JsonResponse
    {
        $cart = $this->CartService->storeCart($request->validated());
        return self::success($cart, 'Cart created successfully', 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Cart $cart): JsonResponse
    {
        return self::success($cart, 'Cart retrieved successfully');
    }

    /**
     * Update the specified resource in storage.
     * @throws \Exception
     */
    public function update(UpdateCartRequest $request, Cart $cart): JsonResponse
    {
        $updatedCart = $this->CartService->updateCart($cart, $request->validated());
        return self::success($updatedCart, 'Cart updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Cart $cart): JsonResponse
    {
        $cart->delete();
        return self::success(null, 'Cart deleted successfully');
    }

    /**
     * Display soft-deleted records.
     */
    public function showDeleted(): JsonResponse
    {
        $carts = Cart::onlyTrashed()->get();
        return self::success($carts, 'Carts retrieved successfully');
    }

    /**
     * Restore a soft-deleted record.
     * @param string $id
     * @return JsonResponse
     */
    public function restoreDeleted(string $id): JsonResponse
    {
        $cart = Cart::onlyTrashed()->findOrFail($id);
        $cart->restore();
        return self::success($cart, 'Cart restored successfully');
    }

    /**
     * Permanently delete a soft-deleted record.
     * @param string $id
     * @return JsonResponse
     */
    public function forceDeleted(string $id): JsonResponse
    {
        $cart = Cart::onlyTrashed()->findOrFail($id)->forceDelete();
        return self::success(null, 'Cart force deleted successfully');
    }
}
