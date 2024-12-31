<?php

namespace App\Http\Controllers\Order;

use App\Http\Requests\Order\DeletedOrderRequest;
use App\Http\Requests\Order\IndexOrderRequest;
use App\Http\Requests\Order\StoreOrderRequest;
use App\Http\Requests\Order\UpdateOrderRequest;
use App\Http\Resources\OrderResource;
use App\Models\Order\Order;
use App\Services\Order\OrderService;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{

    protected OrderService $OrderService;

    public function __construct(OrderService $OrderService)
    {
        $this->OrderService = $OrderService;
    }

    /**
     * Display a listing of the orders.
     * @param \App\Http\Requests\Order\IndexOrderRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(IndexOrderRequest $request): JsonResponse
    {
        $orders = $this->OrderService->getOrders($request->validated());
        return self::paginated($orders, OrderResource::class, 'Orders retrieved successfully', 200);
    }

    /**
     * Store a newly created resource in storage.
     * @param \App\Http\Requests\Order\StoreOrderRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    // public function store(StoreOrderRequest $request): JsonResponse
    // {
    //     $order = $this->OrderService->storeOrder($request->validated());
    //     return self::success($order, 'Order created successfully', 201);
    // }

    /**
     * Display the specified order.
     * @param \App\Models\Order\Order $order
     * @throws \Illuminate\Auth\Access\AuthorizationException
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Order $order): JsonResponse
    {
        if ($order && $order->user_id !== Auth::id()) {
            return self::error(null, 'You do not have permission to access this resource.', 403);
        }
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
     * Display soft-deleted records.
     * @param \App\Http\Requests\Order\DeletedOrderRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function showDeleted(DeletedOrderRequest $request): JsonResponse
    {
        $deletedOrders = $this->OrderService->getDeletedOrders($request->validated());
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
}
