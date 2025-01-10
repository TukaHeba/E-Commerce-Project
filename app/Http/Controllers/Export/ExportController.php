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
    /*
     * Export best categories report.
     */
    public function bestCategoriesExport()
    {
        return $this->ExportService->bestCategoriesExport();
    }
    /*
     * Export best selling products report.
     */
    public function bestSellingProductsExport()
    {
        return $this->ExportService->bestSellingProductsExport();
    }
    /*
     * Export products low on stock report.
     */
    public function productsLowOnStockExport()
    {
        return $this->ExportService->productsLowOnStockExport();
    }
    /*
     * Export orders late to deliver report.
     */
    public function ordersLateToDeliverExport()
    {
        return $this->ExportService->ordersLateToDeliverExport();
    }
    /*
     * Export products never been sold report.
     */
    public function productsNeverBeenSoldExport()
    {
        return $this->ExportService->productsNeverBeenSoldExport();
    }
    /*
     * Export products remaining in cart report.
     */
    public function productsRemainingInCartsExport()
    {
        return $this->ExportService->productsRemainingInCartsExport();
    }
    /*
     * Export countries with highest orders report.
     */
    public function countriesWithHighestOrdersExport(TopCountryRequest $request, int $country = 5)
    {
        return $this->ExportService->countriesWithHighestOrdersExport($request, $country);
    }

}
