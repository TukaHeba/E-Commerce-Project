<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Http\Resources\Report2Resource;
use App\Models\Order\Order;
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

    /**
     * Top 5 countries in terms of sales report
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function topCountries()
    {
        $data = Order::selectRaw('addresses.country, COUNT(orders.id) as total_orders')
            ->join('addresses', 'orders.address_id', '=', 'addresses.id')
            ->groupBy('addresses.country')
            ->orderByDesc('total_orders')
            ->take(5)
            ->get();
        return self::success($data, 'Top 5 countries in terms of sales report');
    }
}
