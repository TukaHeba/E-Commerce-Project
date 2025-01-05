<?php

namespace App\Http\Controllers\Report;


use App\Http\Controllers\Controller;
use App\Http\Requests\Report\Top5CountryRequest;
use App\Http\Resources\ProductResource;
use App\Http\Resources\Report1Resource;
use App\Http\Resources\Report2Resource;
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
     * Report 6
     * The country with the highest number of orders report With the ability to filter by a specific date
     *
     * @param Top5CountryRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function topCountries(Top5CountryRequest $request)
    {
        $result = $this->ReportService->Top5Countries($request->validationData());
        return self::success($result, 'Top 5 countries in terms of sales report');
    }

    public function sendUnsoldProductsEmail()
    {
        // Get the result from the ReportService
        $unsoldProducts = $this->ReportService->sendUnsoldProductsEmail();
        return self::success(ProductResource::collection($unsoldProducts), 'Products never been Sold retrieved successfully', 200);
    }


}
