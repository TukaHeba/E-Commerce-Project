<?php

namespace App\Services\Report;

use App\Models\Product\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class ReportService
{
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
    public function BestSellingProductsReport()
    {
        return Cache::remember("best_selling_products_report", now()->addDay(), function () {
            return Product::bestSelling()->paginate(10);
        });
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
