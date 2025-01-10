<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;
use App\Http\Requests\Report\TopCountryRequest;
use App\Http\Resources\OrderResource;
use App\Http\Resources\ProductResource;
use App\Jobs\SendDelayedOrderEmail;
use App\Services\Report\ReportService;
use Illuminate\Http\Resources\Json\JsonResource;

class ReportController extends Controller
{
    protected ReportService $ReportService;

    public function __construct(ReportService $ReportService)
    {
        $this->ReportService = $ReportService;
    }

    /**
     * Generate a report of orders delayed for delivery
     * @return \Illuminate\Http\JsonResponse
     */
    public function ordersLateToDeliverReport()
    {
        $latingOrders = $this->ReportService->getOrdersLateToDeliver();
        return self::paginated($latingOrders, OrderResource::class, 'Lating orders retrieved successfully', 200);
    }

    /**
     * Products remaining in the cart without being ordered report
     * @return \Illuminate\Http\JsonResponse
     */
    public function productsRemainingInCartsReport()
    {
        $productsRemaining = $this->ReportService->getProductsRemainingInCarts();
        return self::success($productsRemaining, 'Products retrieved successfully', 200);
    }

    /**
     * Products running low on the stock report
     */
    public function productsLowOnStockReport()
    {
        $productsLowOnStock = $this->ReportService->getProductsLowOnStock();
        return self::paginated($productsLowOnStock, ProductResource::class, 'Products retrieved successfully', 200);
    }

    /**
     * Best-selling products for offers report
     */
    public function bestSellingProductsReport()
    {
        $products = $this->ReportService->getBestSellingProducts();
        return self::paginated($products, null, 'Products retrieved successfully', 200);
    }

    /**
     * Best categories report
     */
    public function bestCategoriesReport()
    {
        $BestCategories = $this->ReportService->getBestSellingCategories();
        return self::paginated($BestCategories, null, 'Categories retrieved successfully', 200);
    }

    /**
     * Report 6
     * The country with the highest number of orders report With the ability to filter by a specific date
     *
     * @param TopCountryRequest $request
     * @param $country
     * @return \Illuminate\Http\JsonResponse
     */

    public function countriesWithHighestOrdersReport(TopCountryRequest $request, int $country = 5)
    {
        $result = $this->ReportService->getCountriesWithHighestOrders($request->validationData(),$country);
        return self::success($result, 'Countries With Highest Orders Report');
    }

    public function productsNeverBeenSoldReport()
    {
        $unsoldProducts = $this->ReportService->getProductsNeverBeenSold();
        return self::paginated($unsoldProducts, null, 'Products never been Sold retrieved successfully', 200);
    }
}
