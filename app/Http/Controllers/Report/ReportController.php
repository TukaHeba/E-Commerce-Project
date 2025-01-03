<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;
use App\Http\Resources\Report1Resource;
use App\Http\Resources\Report2Resource;
use App\Services\Report\ReportService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

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
        return self::paginated($latingOrders , Report1Resource::class , 'Lating orders retrieved successfully',200);
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
    public function repor3()
    {
        //
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
}
