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
        if ($order->user_id !== Auth::id()) {
            return self::error(null, 'You do not have permission to access this resource.', 403);
        }
        $order->load('orderItems');
        return self::success(new OrderResource($order), 'Order retrieved successfully');
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
        $destroiedOrder = $this->OrderService->destroyOrder($order);
        return $destroiedOrder['status']
            ? self::success(null, 'Order deleted successfully')
            : self::error(new OrderResource($order), $destroiedOrder['msg'], $destroiedOrder['code']);
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
}
