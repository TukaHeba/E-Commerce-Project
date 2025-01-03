<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;
use App\Services\Report\ReportService;
use App\Http\Resources\ProductResource;
use App\Http\Resources\Report2Resource;
use App\Http\Resources\SubMainCategoryResource;

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
    public function BestCategories()
    {
        $BestCategories = $this->ReportService->BestCategories();
        return self::paginated($BestCategories, SubMainCategoryResource::class, 'Categories retrieved successfully', 200);
    }

    /**
     * The country with the highest number of orders report
     */
    public function repor6()
    {
        //
    }
}
