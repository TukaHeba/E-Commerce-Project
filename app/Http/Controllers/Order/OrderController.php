<?php

namespace App\Http\Controllers\Order;

use App\Http\Requests\Order\StoreOrderRequest;
use App\Http\Requests\Order\UpdateOrderRequest;
use App\Models\Order\Order;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class OrderController extends Controller
{
  
    protected OrderService $OrderService;

    public function __construct(OrderService $OrderService)
    {
        $this->OrderService = $OrderService;
    }

    /**
     * Display a listing of the resource.
     * @throws \Exception
     */
    public function index(Request $request): JsonResponse
    {
        $orders = $this->OrderService->getOrders($request);
        return self::paginated($orders, 'Orders retrieved successfully', 200);
    }

    /**
     * Store a newly created resource in storage.
     * @throws \Exception
     */
    public function store(StoreOrderRequest $request): JsonResponse
    {
        $order = $this->OrderService->storeOrder($request->validated());
        return self::success($order, 'Order created successfully', 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Order $order): JsonResponse
    {
        return self::success($order, 'Order retrieved successfully');
    }

    /**
     * Update the specified resource in storage.
     * @throws \Exception
     */
    public function update(UpdateOrderRequest $request, Order $order): JsonResponse
    {
        $updatedOrder = $this->OrderService->updateOrder($order, $request->validated());
        return self::success($updatedOrder, 'Order updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Order $order): JsonResponse
    {
        $order->delete();
        return self::success(null, 'Order deleted successfully');
    }

    /**
     * Display soft-deleted records.
     */
    public function showDeleted(): JsonResponse
    {
        $orders = Order::onlyTrashed()->get();
        return self::success($orders, 'Orders retrieved successfully');
    }

    /**
     * Restore a soft-deleted record.
     * @param string $id
     * @return JsonResponse
     */
    public function restoreDeleted(string $id): JsonResponse
    {
        $order = Order::onlyTrashed()->findOrFail($id);
        $order->restore();
        return self::success($order, 'Order restored successfully');
    }

    /**
     * Permanently delete a soft-deleted record.
     * @param string $id
     * @return JsonResponse
     */
    public function forceDeleted(string $id): JsonResponse
    {
        $order = Order::onlyTrashed()->findOrFail($id)->forceDelete();
        return self::success(null, 'Order force deleted successfully');
    }
}
