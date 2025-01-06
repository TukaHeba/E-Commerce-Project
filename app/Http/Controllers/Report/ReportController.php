<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Http\Resources\Report1Resource;
use App\Jobs\SendDelayedOrderEmail;
use App\Services\Report\ReportService;


class ReportController extends Controller
{
    protected ReportService $ReportService;

    public function __construct(ReportService $ReportService)
    {
        $this->ReportService = $ReportService;
    }

    /**
     * Orders late to deliver report
     */
    public function ordersLateToDeliverReport()
    {
        $latingOrders = $this->ReportService->getOrdersLateToDeliver();
        return self::paginated($latingOrders, Report1Resource::class, 'Lating orders retrieved successfully', 200);
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
       return self::paginated($products, null,'Products retrieved successfully', 200);
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
     * The country with the highest number of orders report
     * @return \Illuminate\Http\JsonResponse
     */
    public function countriesWithHighestOrdersReport()
    {
        $data = $this->ReportService->getCountriesWithHighestOrders();
        return self::success($data, 'Top 5 countries in terms of sales report');
    }

    public function productsNeverBeenSoldReport()
    {
        // Get the result from the ReportService
        $unsoldProducts = $this->ReportService->getProductsNeverBeenSold();
        return self::success(ProductResource::collection($unsoldProducts), 'Products never been Sold retrieved successfully', 200);
    }
}
