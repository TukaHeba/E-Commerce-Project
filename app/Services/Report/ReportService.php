<?php

namespace App\Services\Report;

use App\Models\Cart\Cart;
use App\Models\Order\Order;
use Illuminate\Http\Request;
use App\Models\Product\Product;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;


class ReportService
{
    /**
     * Orders late to deliver report
     */
    public function getOrdersLateToDeliver()
    {
        $sevenDaysAgo = Carbon::now()->subDays(7); // Create the current date and subtract 7 days from it

        // Select orders with status 'shipped' where the last update was 7 or more days ago
        $lating_orders = Order::with(['user','zone.city:id,name'])->select('id','user_id','zone_id','postal_code','status','total_price','order_number')
            ->where('status',  'shipped')
            ->where('updated_at', '<=', $sevenDaysAgo)->paginate(10);

        return $lating_orders;
    }

    /**
     * Products remaining in the cart without being ordered report.
     *
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function getProductsRemainingInCarts()
    {
        $products_remaining = Cart::withWhereHas('cartItems', function ($query) {
            $query->where('created_at', '<=', Carbon::now()->subMonths(2))
                ->with('product:id,name');
        })->select('id', 'user_id')->paginate(10);

        return $products_remaining;
    }

    /**
     * Products running low on the stock report
     */
    public function getProductsLowOnStock()
    {
        return Product::lowStock()->paginate(10);
    }


    /**
     * Best-selling products for report
     */
    public function getBestSellingProducts()
    {
        return Product::bestSelling('product_with_total_sold')->paginate(10);
    }

    /**
     * Best categories report
     */
    public function getBestSellingCategories()
    {
        return Product::bestSelling('category_with_total_sold')->paginate(10);
    }

    /**
     * The products never been sold
     *
     * @return LengthAwarePaginator
     */
    public function getProductsNeverBeenSold()
    {
        return Product::neverBeenSold()->paginate(10);
    }


    /**
     * The country with the highest number of orders report With the ability to filter by a specific date
     *
     * @param array $data
     * @param int $country
     * @return mixed
     */

    public function getCountriesWithHighestOrders(array $data, int $country)
    {
        $topCountries = Order::with('zone.city.country')
            ->when(isset($data['start_date']), function ($q) use ($data) {
                return $q->whereDate('created_at', '>=', $data['start_date']);
            })
            ->when(isset($data['end_date']), function ($q) use ($data) {
                return $q->whereDate('created_at', '<=', $data['end_date']);
            })
            ->get()
            ->groupBy(fn($order) => $order->zone->city->country->name) // تجميع حسب اسم الدولة
            ->map(fn($orders, $countryName) => [
                'country_name' => $countryName,
                'total_orders' => $orders->count(),
            ])
            ->sortByDesc('total_orders')
            ->take($country)
            ->values();
        return $topCountries;
    }
}
