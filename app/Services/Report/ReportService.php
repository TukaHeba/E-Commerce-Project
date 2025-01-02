<?php

namespace App\Services\Report;

use Carbon\Carbon;
use App\Models\Product\Product;
use App\Models\CartItem\CartItem;

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
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function repor2()
    {
        $products_remaining = CartItem::where('created_at', '<=', Carbon::now()->subMonths(2))
            ->paginate(10);

        return $products_remaining;
    }

    /**
     * Products running low on the stock report
     */
    public function ProductsLowOnStockReport()
    {
        return $lowStockProducts = Product::lowStock()->paginate(10);
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
