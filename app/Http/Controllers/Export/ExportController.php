<?php

namespace App\Http\Controllers\Export;

use App\Http\Controllers\Controller;
use App\Services\Export\ExportService;
use App\Http\Requests\Report\TopCountryRequest;

class ExportController extends Controller
{
    protected ExportService $ExportService;

    public function __construct(ExportService $ExportService)
    {
        $this->ExportService = $ExportService;
    }

    public function bestCategoriesExport()
    {
        $this->ExportService->bestCategoriesExport();
        return self::success(null, 'best Categories Export successfully', 201);
    }



    public function bestSellingProductsExport()
    {
        $this->ExportService->bestCategoriesExport();
        return self::success(null, 'best Selling Products Export successfully', 201);
    }



    public function productsLowOnStockExport()
    {
        $this->ExportService->bestCategoriesExport();
        return self::success(null, 'products Low On Stock Export successfully', 201);
    }


    public function ordersLateToDeliverExport()
    {
        $this->ExportService->bestCategoriesExport();
        return self::success(null, 'orders Late To Deliver Export successfully', 201);
    }


    public function productsNeverBeenSoldExport()
    {
        $this->ExportService->bestCategoriesExport();
        return self::success(null, 'products Never Been Sold Export successfully', 201);
    }


    public function productsRemainingInCartsExport()
    {
        $this->ExportService->bestCategoriesExport();
        return self::success(null, 'products Remaining In Carts Export successfully', 201);
    }




    public function countriesWithHighestOrdersExport(TopCountryRequest $request, int $country = 5)
    {
        $this->ExportService->bestCategoriesExport();
        return self::success(null, 'countries With Highest Orders Export successfully', 201);
    }

}
