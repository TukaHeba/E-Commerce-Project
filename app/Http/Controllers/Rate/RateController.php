<?php

namespace App\Http\Controllers\Rate;

use App\Models\Rate\Rate;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Services\Rate\RateService;
use App\Http\Controllers\Controller;
use App\Http\Resources\RateResource;
use App\Http\Requests\Rate\StoreRateRequest;
use App\Http\Requests\Rate\UpdateRateRequest;

class RateController extends Controller
{

    protected RateService $RateService;

    public function __construct(RateService $RateService)
    {
        $this->RateService = $RateService;
    }

    /**
     * Display a paginated list of rates.
     * @param Request $request The HTTP request object containing query parameters.
     * @return JsonResponse A JSON response with paginated rate data.
     * @throws \Exception
     */
    public function index(Request $request): JsonResponse
    {
        $rates = $this->RateService->getRates($request);
        return self::paginated($rates, RateResource::class, 'Rates retrieved successfully', 200);
    }

    /**
     * Store a newly created rate in the database.
     * @param StoreRateRequest $request The HTTP request object containing validated rate data.
     * @return JsonResponse A JSON response with the created rate.
     * @throws \Exception
     */
    public function store(StoreRateRequest $request): JsonResponse
    {
        $data = $request->validated();
        $data['user_id'] = auth()->id();
        $rate = $this->RateService->storeRate($data);
        return self::success(new RateResource($rate), 'Rate created successfully', 201);
    }

    /**
     * Display a specific rate.
     * @param Rate $rate The rate model instance to be retrieved.
     * @return JsonResponse A JSON response with the rate data.
     */
    public function show(Rate $rate): JsonResponse
    {
        $rate = $rate->load(['user', 'product']);
        return self::success(new RateResource($rate), 'Rate retrieved successfully', 200);
    }

    /**
     * Update an existing rate.
     * @param UpdateRateRequest $request The HTTP request object containing validated update data.
     * @param Rate $rate The rate model instance to be updated.
     * @return JsonResponse A JSON response with the updated rate data.
     * @throws \Exception
     */
    public function update(UpdateRateRequest $request, Rate $rate): JsonResponse
    {
        $updatedRate = $this->RateService->updateRate($rate, $request->validated());
        return self::success(new RateResource($updatedRate), 'Rate updated successfully', 201);
    }

    /**
     * Delete a specific rate from the database.
     * @param Rate $rate The rate model instance to be deleted.
     * @return JsonResponse A JSON response confirming the deletion or an error if unauthorized.
     */
    public function destroy(Rate $rate): JsonResponse
    {
        if ($rate->user_id === auth()->id()) {
            $this->RateService->destroy($rate);
            return self::success(null, 'Rate deleted successfully', 200);
        } else {
            return self::error(null, "You're not authorized to delete this rate.", 403);
        }
    }
}
