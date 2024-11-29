<?php

namespace App\Http\Controllers\Rate;

use App\Http\Requests\Rate\StoreRateRequest;
use App\Http\Requests\Rate\UpdateRateRequest;
use App\Models\Rate\Rate;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class RateController extends Controller
{
  
    protected RateService $RateService;

    public function __construct(RateService $RateService)
    {
        $this->RateService = $RateService;
    }

    /**
     * Display a listing of the resource.
     * @throws \Exception
     */
    public function index(Request $request): JsonResponse
    {
        $rates = $this->RateService->getRates($request);
        return self::paginated($rates, 'Rates retrieved successfully', 200);
    }

    /**
     * Store a newly created resource in storage.
     * @throws \Exception
     */
    public function store(StoreRateRequest $request): JsonResponse
    {
        $rate = $this->RateService->storeRate($request->validated());
        return self::success($rate, 'Rate created successfully', 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Rate $rate): JsonResponse
    {
        return self::success($rate, 'Rate retrieved successfully');
    }

    /**
     * Update the specified resource in storage.
     * @throws \Exception
     */
    public function update(UpdateRateRequest $request, Rate $rate): JsonResponse
    {
        $updatedRate = $this->RateService->updateRate($rate, $request->validated());
        return self::success($updatedRate, 'Rate updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Rate $rate): JsonResponse
    {
        $rate->delete();
        return self::success(null, 'Rate deleted successfully');
    }

    /**
     * Display soft-deleted records.
     */
    public function showDeleted(): JsonResponse
    {
        $rates = Rate::onlyTrashed()->get();
        return self::success($rates, 'Rates retrieved successfully');
    }

    /**
     * Restore a soft-deleted record.
     * @param string $id
     * @return JsonResponse
     */
    public function restoreDeleted(string $id): JsonResponse
    {
        $rate = Rate::onlyTrashed()->findOrFail($id);
        $rate->restore();
        return self::success($rate, 'Rate restored successfully');
    }

    /**
     * Permanently delete a soft-deleted record.
     * @param string $id
     * @return JsonResponse
     */
    public function forceDeleted(string $id): JsonResponse
    {
        $rate = Rate::onlyTrashed()->findOrFail($id)->forceDelete();
        return self::success(null, 'Rate force deleted successfully');
    }
}
