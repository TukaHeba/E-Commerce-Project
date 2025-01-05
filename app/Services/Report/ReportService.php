<?php

namespace App\Services\Report;

use App\Models\CartItem\CartItem;
use App\Models\Order\Order;
use Carbon\Carbon;
use App\Models\Product\Product;
use App\Models\Category\MainCategorySubCategory;
use Illuminate\Support\Facades\DB;
use App\Models\OrderItem\OrderItem;
use App\Models\User\User;
use App\Jobs\SendUnsoldProductEmail;
use Illuminate\Support\Facades\Artisan;


class ReportService
{
    /**
     * Orders late to deliver report
     */
    public function repor1()
    {
        $sevenDaysAgo = Carbon::now()->subDays(7); // Create the current date and subtract 7 days from it

        $lating_orders = Order::where('status','shipped')
            ->where('created_at','<=',$sevenDaysAgo)->paginate(10);

        return $lating_orders ;
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
        return Product::lowStock()->paginate(10);
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
    public function BestCategories()
    {
        return $BestCategories = Product::Selling()->paginate(10);
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
     * The country with the highest number of orders report
     * @return mixed
     */

    public function Top5Countries(){
        $data = Order::selectRaw('addresses.country, COUNT(orders.id) as total_orders')
            ->join('addresses', 'orders.address_id', '=', 'addresses.id')
            ->groupBy('addresses.country')
            ->orderByDesc('total_orders')
            ->take(5)
            ->get();
        return $data;
    }

}
