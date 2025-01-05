<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Http\Resources\Report1Resource;
use App\Http\Resources\SubMainCategoryResource;
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
    public function repor1()
    {
        $latingOrders = $this->ReportService->repor1();
        return self::paginated($latingOrders, Report1Resource::class, 'Lating orders retrieved successfully', 200);
    }

    /**
     * Products remaining in the cart without being ordered report
     * @return \Illuminate\Http\JsonResponse
     */
    public function productsRemainingReport()
    {
        $productsRemaining = $this->ReportService->getProductsRemaining();
        return self::success($productsRemaining, 'Products retrieved successfully', 200);
    }

    /**
     * Products running low on the stock report
     */
    public function ProductsLowOnStockReport()
    {
        $productsLowOnStock = $this->ReportService->ProductsLowOnStockReport();
        return self::paginated($productsLowOnStock, ProductResource::class, 'Products retrieved successfully', 200);
    }

    /**
     * Best-selling products for offers report
     */
    public function repor4()
    {
        //
    }

    /**
     * Best categories report
     */
    public function BestCategories()
    {
        $BestCategories = $this->ReportService->BestCategories();
        return self::paginated($BestCategories, SubMainCategoryResource::class, 'Categories retrieved successfully', 200);
    }

    /**
     * Report 6
     * The country with the highest number of orders report
     * @return \Illuminate\Http\JsonResponse
     */
    public function topCountries()
    {
        $data = $this->ReportService->Top5Countries();
        return self::success($data, 'Top 5 countries in terms of sales report');
    }

    public function sendUnsoldProductsEmail()
    {
        // Get the result from the ReportService
        $unsoldProducts = $this->ReportService->sendUnsoldProductsEmail();
        return self::success(ProductResource::collection($unsoldProducts), 'Products never been Sold retrieved successfully', 200);
    }


}
