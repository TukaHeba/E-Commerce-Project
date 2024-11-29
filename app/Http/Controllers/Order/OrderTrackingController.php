<?php

namespace App\Http\Controllers\OrderTracking;

use App\Http\Requests\OrderTracking\StoreOrderTrackingRequest;
use App\Http\Requests\OrderTracking\UpdateOrderTrackingRequest;
use App\Models\OrderTracking\OrderTracking;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class OrderTrackingController extends Controller
{
  
    protected OrderTrackingService $OrderTrackingService;

    public function __construct(OrderTrackingService $OrderTrackingService)
    {
        $this->OrderTrackingService = $OrderTrackingService;
    }

    /**
     * Display a listing of the resource.
     * @throws \Exception
     */
    public function index(Request $request): JsonResponse
    {
        $orderTrackings = $this->OrderTrackingService->getOrderTrackings($request);
        return self::paginated($orderTrackings, 'OrderTrackings retrieved successfully', 200);
    }

    /**
     * Store a newly created resource in storage.
     * @throws \Exception
     */
    public function store(StoreOrderTrackingRequest $request): JsonResponse
    {
        $orderTracking = $this->OrderTrackingService->storeOrderTracking($request->validated());
        return self::success($orderTracking, 'OrderTracking created successfully', 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(OrderTracking $orderTracking): JsonResponse
    {
        return self::success($orderTracking, 'OrderTracking retrieved successfully');
    }

    /**
     * Update the specified resource in storage.
     * @throws \Exception
     */
    public function update(UpdateOrderTrackingRequest $request, OrderTracking $orderTracking): JsonResponse
    {
        $updatedOrderTracking = $this->OrderTrackingService->updateOrderTracking($orderTracking, $request->validated());
        return self::success($updatedOrderTracking, 'OrderTracking updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(OrderTracking $orderTracking): JsonResponse
    {
        $orderTracking->delete();
        return self::success(null, 'OrderTracking deleted successfully');
    }

    /**
     * Display soft-deleted records.
     */
    public function showDeleted(): JsonResponse
    {
        $orderTrackings = OrderTracking::onlyTrashed()->get();
        return self::success($orderTrackings, 'OrderTrackings retrieved successfully');
    }

    /**
     * Restore a soft-deleted record.
     * @param string $id
     * @return JsonResponse
     */
    public function restoreDeleted(string $id): JsonResponse
    {
        $orderTracking = OrderTracking::onlyTrashed()->findOrFail($id);
        $orderTracking->restore();
        return self::success($orderTracking, 'OrderTracking restored successfully');
    }

    /**
     * Permanently delete a soft-deleted record.
     * @param string $id
     * @return JsonResponse
     */
    public function forceDeleted(string $id): JsonResponse
    {
        $orderTracking = OrderTracking::onlyTrashed()->findOrFail($id)->forceDelete();
        return self::success(null, 'OrderTracking force deleted successfully');
    }
}
