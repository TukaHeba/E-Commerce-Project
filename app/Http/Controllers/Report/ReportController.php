<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;
use App\Services\Report\ReportService;
use Illuminate\Http\Request;

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
     */
    public function repor2()
    {
        //
    }

    /**
     * Products running low on the stock report
     */
    public function repor3()
    {
        //
    }

    /**
     * Best-selling products for offers report
     */
    public function generateBestSellingProductsReport()
    {
       $products = $this->ReportService->BestSellingProductsReport();
       return self::paginated($products, null,'Products retrieved successfully', 200);
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
}
