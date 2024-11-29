<?php

namespace App\Http\Controllers\Order;

use App\Http\Requests\OrderItem\StoreOrderItemRequest;
use App\Http\Requests\OrderItem\UpdateOrderItemRequest;
use App\Models\OrderItem\OrderItem;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class OrderItemController extends Controller
{
  
    protected OrderItemService $OrderItemService;

    public function __construct(OrderItemService $OrderItemService)
    {
        $this->OrderItemService = $OrderItemService;
    }

    /**
     * Display a listing of the resource.
     * @throws \Exception
     */
    public function index(Request $request): JsonResponse
    {
        $orderItems = $this->OrderItemService->getOrderItems($request);
        return self::paginated($orderItems, 'OrderItems retrieved successfully', 200);
    }

    /**
     * Store a newly created resource in storage.
     * @throws \Exception
     */
    public function store(StoreOrderItemRequest $request): JsonResponse
    {
        $orderItem = $this->OrderItemService->storeOrderItem($request->validated());
        return self::success($orderItem, 'OrderItem created successfully', 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(OrderItem $orderItem): JsonResponse
    {
        return self::success($orderItem, 'OrderItem retrieved successfully');
    }

    /**
     * Update the specified resource in storage.
     * @throws \Exception
     */
    public function update(UpdateOrderItemRequest $request, OrderItem $orderItem): JsonResponse
    {
        $updatedOrderItem = $this->OrderItemService->updateOrderItem($orderItem, $request->validated());
        return self::success($updatedOrderItem, 'OrderItem updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(OrderItem $orderItem): JsonResponse
    {
        $orderItem->delete();
        return self::success(null, 'OrderItem deleted successfully');
    }

    /**
     * Display soft-deleted records.
     */
    public function showDeleted(): JsonResponse
    {
        $orderItems = OrderItem::onlyTrashed()->get();
        return self::success($orderItems, 'OrderItems retrieved successfully');
    }

    /**
     * Restore a soft-deleted record.
     * @param string $id
     * @return JsonResponse
     */
    public function restoreDeleted(string $id): JsonResponse
    {
        $orderItem = OrderItem::onlyTrashed()->findOrFail($id);
        $orderItem->restore();
        return self::success($orderItem, 'OrderItem restored successfully');
    }

    /**
     * Permanently delete a soft-deleted record.
     * @param string $id
     * @return JsonResponse
     */
    public function forceDeleted(string $id): JsonResponse
    {
        $orderItem = OrderItem::onlyTrashed()->findOrFail($id)->forceDelete();
        return self::success(null, 'OrderItem force deleted successfully');
    }
}
