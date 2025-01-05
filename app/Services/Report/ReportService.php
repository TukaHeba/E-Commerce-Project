<?php

namespace App\Services\Report;

use App\Jobs\SendUnsoldProductEmail;
use App\Models\CartItem\CartItem;
use App\Models\Order\Order;
use App\Models\Product\Product;
use App\Models\User\User;
use Carbon\Carbon;


class ReportService
{
    /**
     * Orders late to deliver report
     */
    public function repor1()
    {
        $sevenDaysAgo = Carbon::now()->subDays(7); // Create the current date and subtract 7 days from it

        $lating_orders = Order::where('status', 'shipped')
            ->where('created_at', '<=', $sevenDaysAgo)->paginate(10);

        return $lating_orders;
    }

    /**
     * Products remaining in the cart without being ordered report
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getProductsRemaining()
    {
        $products_remaining = CartItem::with('product')
            ->where('created_at', '<=', Carbon::now()->subMonths(2))
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

    /**
     * The products never been sold
     */
    public function sendUnsoldProductsEmail()
    {
        // Fetch all users with the role 'sales manager'
        $user = User::role('sales manager')->first();
        // Dispatch the job for each user and collect the results
        $job = new SendUnsoldProductEmail($user);
        $job->handle(); // Execute the job synchronously
        $result = $job->getUnsoldProducts(); // Get the result
        return $result;
    }


    /**
     * The country with the highest number of orders report With the ability to filter by a specific date
     *
     * @param array $data
     * @return mixed
     */

    public function Top5Countries(array $data)
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
            ->take(5) // إرجاع أفضل 5 دول
            ->values();
        return $topCountries;
    }

}
