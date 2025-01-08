<?php

namespace App\Services\Report;

use App\Models\Cart\Cart;
use App\Models\Order\Order;
use Illuminate\Http\Request;
use App\Models\Product\Product;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

class ReportService
{
    /**
     * Orders late to deliver report
     */
    public function getOrdersLateToDeliver()
    {
        $sevenDaysAgo = Carbon::now()->subDays(7); // Create the current date and subtract 7 days from it

        $lating_orders = Order::where('status', 'shipped')
            ->where('created_at', '<=', $sevenDaysAgo)->paginate(10);

        return $lating_orders;
    }

    /**
     * Products remaining in the cart without being ordered report
     * @return array
     */
    public function getProductsRemainingInCarts(): array
    {
        $products_remaining = Cart::withWhereHas('cartItems', function ($query) {
                $query->where('created_at', '<=', Carbon::now()->subMonths(2))
                    ->with(['product:id,name'])
                    ->select('cart_id', 'product_id', 'created_at');
            }
        )->select('id', 'user_id')
         ->get();

        return $products_remaining->toArray();
    }

    /**
     * Products running low on the stock report
     */
    public function getProductsLowOnStock()
    {
        return Product::lowStock()->paginate(10);
    }


    /**
     * Best-selling products for offers report
     */
    public function getBestSellingProducts()
    {
        return Cache::remember("best_selling_products_report", now()->addDay(), function () {
            return Product::bestSelling('product_with_total_sold')->paginate(10);
        });
    }

    /**
     * Best categories report
     */
    public function getBestSellingCategories()
    {
        return $BestCategories = Product::bestSelling('category_with_total_sold')->paginate(10);
    }

    /**
     * The products never been sold
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
            ->sortByDesc('total_orders') // ترتيب تنازلي حسب عدد الطلبات
            ->take($country) // إرجاع أفضل 5 دول
            ->values();
        return $topCountries;
    }
}
