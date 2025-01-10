<?php

namespace App\Http\Controllers\Order;

use App\Models\Order\Order;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Services\Order\OrderService;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\OrderResource;
use App\Http\Requests\Order\UpdateOrderRequest;

class OrderController extends Controller
{

    protected OrderService $OrderService;

    public function __construct(OrderService $OrderService)
    {
        $this->OrderService = $OrderService;
    }

    /**
     * Display a listing of the orders related to user.
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function indexUser(Request $request): JsonResponse
    {
        $this->authorize('viewOrdersUser', Order::class);
        $orders = $this->OrderService->getOrdersUser($request);
        return self::paginated($orders, OrderResource::class, 'Orders retrieved successfully', 200);
    }

    /**
     * Display a listing of the orders related to admin.
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function indexAdmin(Request $request): JsonResponse
    {
        $this->authorize('viewOrdersAdmin', Order::class);
        $orders = $this->OrderService->getOrdersAdmin($request);
        return self::paginated($orders, OrderResource::class, 'Orders retrieved successfully', 200);
    }

    /**
     * Display the specified order.
     * @param \App\Models\Order\Order $order
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Order $order): JsonResponse
    {
        $this->authorize('show', $order);
        return self::success(new OrderResource($order->load('orderItems')), 'Order retrieved successfully');
    }

    /**
     * Update the specified order in storage.
     * @param \App\Http\Requests\Order\UpdateOrderRequest $request
     * @param \App\Models\Order\Order $order
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateOrderRequest $request, Order $order): JsonResponse
    {
        $this->authorize('update' , Order::class);
        $updatedOrder = $this->OrderService->updateOrder($order, $request->validated());
        return self::success($updatedOrder, 'Order updated successfully');
    }

    /**
     * Remove the specified order from storage.
     * @param \App\Models\Order\Order $order
     * @return JsonResponse
     */
    public function destroy(Order $order)
    {
        $this->authorize('delete' , Order::class);
        $order->delete();
        return self::success(null, 'Order deleted successfully');

    }

    /**
     * Display soft-deleted records related to admin.
     * @param \Illuminate\Support\Facades\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function showDeleted(Request $request): JsonResponse
    {
        $this->authorize('showDeleted', arguments: Order::class);
        $deletedOrders = $this->OrderService->getDeletedOrdersAdmin($request);
        return self::paginated($deletedOrders, OrderResource::class, 'Orders retrieved successfully', 200);
    }

    /**
     * Restore a soft-deleted record.
     * @param string $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function restoreDeleted(string $id): JsonResponse
    {
        $this->authorize('restoreDeleted', arguments: Order::class);
        $order = Order::onlyTrashed()->where('user_id', Auth::id())->findOrFail($id);
        $order->restore();
        return self::success($order, 'Order restored successfully');
    }

    /**
     * Permanently delete a soft-deleted record.
     * @param string $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function forceDeleted(string $id): JsonResponse
    {
        $this->authorize('forceDeleted', arguments: Order::class);
        $order = Order::onlyTrashed()->findOrFail($id);
        $order->forceDelete();
        return self::success(null, 'Order force deleted successfully');
    }

    /**
     * Retrieve order tracking details for a given order.
     *
     * @param \App\Models\Order\Order $order
     * @return \Illuminate\Http\JsonResponse
     */
    public function orderTracking(Order $order): JsonResponse
    {
        $this->authorize('OrderTracking_oldest_lastest', Order::class);
        $order = $this->OrderService->getOrderTracking($order);
        return self::success(new OrderResource($order), 'Order tracking data retrieved successfully.');
    }

    /**
     * Display oldest order in storage.
     * @return JsonResponse
     */
    public function showOldestOrder(): JsonResponse
    {
        $this->authorize('OrderTracking_oldest_lastest', Order::class);
        $order = $this->OrderService->getOldestOrder();
        return self::success(new OrderResource($order->load('orderItems')), 'Oldest order retrieved successfully');
    }

    /**
     * Display latest order in storage.
     * @return JsonResponse
     */
    public function showLatestOrder(): JsonResponse
    {
        $this->authorize('OrderTracking_oldest_lastest', Order::class);
        $order = $this->OrderService->getLatestOrder();
        return self::success(new OrderResource($order->load('orderItems')), 'Latest order retrieved successfully');
    }

}
