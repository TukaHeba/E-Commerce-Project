<?php

namespace App\Services\Report;

use App\Models\Cart\Cart;
use App\Models\CartItem\CartItem;
use App\Models\Order\Order;
use Carbon\Carbon;
use App\Models\Product\Product;
use App\Models\User\User;
use App\Jobs\SendUnsoldProductEmail;
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
        $lating_orders = Order::where('status', 'shipped')
            ->where('created_at', '<=', $sevenDaysAgo)->paginate(10);

        return $lating_orders;
        return $lating_orders;
    }

    /**
     * Products remaining in the cart without being ordered report
     * @return array|\Illuminate\Database\Eloquent\Collection
     */
    public function getProductsRemainingInCarts()
    {
        $products_remaining = Cart::whereHas(
            'cartItems',
            function ($query) {
                $query->where('created_at', '<=', Carbon::now()->subMonths(2));
            }
        )
            ->with([
                'cartItems' => function ($query) {
                    $query->select('cart_id', 'product_id', 'created_at')
                        ->where('created_at', '<=', Carbon::now()->subMonths(2));
                }
            ])
            ->select('id', 'user_id')
            ->get();

        $products_remaining->each(function ($cart) {
            $cart->cartItems->each(function ($item) {
                $item->makeHidden('cart_id');
            });
        });

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
     * Best-selling products for offers report
     */
    public function getBestSellingProducts()
    {
        return Cache::remember("best_selling_products_report", now()->addDay(), function () {
            return Product::bestSelling()->paginate(10);
        });
    }

    /**
     * Best categories report
     */
    public function getBestCategories()
    {
        return $BestCategories = Product::Selling()->paginate(10);
    }

    /**
     * The products never been sold
     */
    public function getProductsNeverBeenSold()
    {
        // Fetch all users with the role 'sales manager'
        $user = User::role('sales manager')->first();
        // Dispatch the job for each user and collect the results
        $job = new SendUnsoldProductEmail($user);
        $job->handle(); // Execute the job synchronously
        $result = $job->getUnsoldProducts(); // Get the result
        $job = new SendUnsoldProductEmail($user);
        $job->handle(); // Execute the job synchronously
        $result = $job->getUnsoldProducts(); // Get the result
        return $result;
    }


    /**
     * The country with the highest number of orders report
     * @return mixed
     */

    public function getCountriesWithHighestOrders()
    {
        $data = Order::selectRaw('addresses.country, COUNT(orders.id) as total_orders')
            ->join('addresses', 'orders.address_id', '=', 'addresses.id')
            ->groupBy('addresses.country')
            ->orderByDesc('total_orders')
            ->take(5)
            ->get();
        return $data;
    }
}
