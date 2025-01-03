<?php

namespace App\Http\Controllers\Report;

use Illuminate\Http\Request;
use App\Models\User\User;
use App\Jobs\SendDelayedOrderEmail;
use App\Http\Controllers\Controller;
use App\Services\Report\ReportService;
use App\Http\Resources\Report2Resource;
use App\Http\Resources\ProductResource;

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
        //
    }

    /**
     * Products remaining in the cart without being ordered report
     * @return \Illuminate\Http\JsonResponse
     */
    public function repor2()
    {
        $productsRemaining = $this->ReportService->repor2();
        return self::paginated($productsRemaining, Report2Resource::class, 'Products retrieved successfully', 200);
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
    public function repor5()
    {
        //
    }

    /**
     * The country with the highest number of orders report
     */
    public function repor6()
    {
        //
    }
    public function sendUnsoldProductsEmail()
    {
        // Get the result from the ReportService
        $unsoldProducts = $this->ReportService->sendUnsoldProductsEmail();
        return self::success(ProductResource::collection($unsoldProducts), 'Products never been Sold retrieved successfully', 200);
    }


}
