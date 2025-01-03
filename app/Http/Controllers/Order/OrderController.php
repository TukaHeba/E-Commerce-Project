<?php

namespace App\Http\Controllers\Order;

use App\Http\Requests\Order\UpdateOrderRequest;
use App\Http\Resources\OrderResource;
use App\Models\Order\Order;
use App\Services\Order\OrderService;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
        $this->authorize('destroy', $order);
        $order->delete();
        return self::success(null, 'Order deleted successfully');

    }

    /**
     * Display soft-deleted records related to user.
     * @param \Illuminate\Support\Facades\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function showDeletedUser(Request $request): JsonResponse
    {
        $this->authorize('viewOrdersUser', Order::class);
        $deletedOrders = $this->OrderService->getDeletedOrdersUser($request);
        return self::paginated($deletedOrders, OrderResource::class, 'Orders retrieved successfully', 200);
    }

    /**
     * Display soft-deleted records related to admin.
     * @param \Illuminate\Support\Facades\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function showDeletedAdmin(Request $request): JsonResponse
    {
        $this->authorize('viewOrdersAdmin', Order::class);
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
        $order = Order::onlyTrashed()->where('user_id', Auth::id())->findOrFail($id)->forceDelete();
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
        $order = $this->OrderService->getOrderTracking($order);
        return self::success(new OrderResource($order), 'Order tracking data retrieved successfully.');
    }

    /**
     * Display oldest order in storage.
     * @return JsonResponse
     */
    public function getOldestOrder()
    {
        $order = Order::oldestOrder()->first();
        return self::success(new OrderResource($order->load('orderItems')), 'Oldest order retrieved successfully');
    }

    /**
     * Display latest order in storage.
     * @return JsonResponse
     */
    public function getLatestOrder()
    {
        $order = Order::latestOrder()->first();
        return self::success(new OrderResource($order->load('orderItems')), 'Latest order retrieved successfully');
    }

}
