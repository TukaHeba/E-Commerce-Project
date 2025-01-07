<?php

namespace App\Services\Export;

use Carbon\Carbon;
use App\Models\Cart\Cart;
use App\Models\Order\Order;
use App\Models\Product\Product;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use App\Http\Requests\Report\TopCountryRequest;
use Symfony\Component\HttpFoundation\StreamedResponse;


class ExportService
{
    /*
     * Export best categories report and save it on storeg.
     */
    public function bestCategoriesExport()
    {
        $BestCategories = Product::bestSelling('category_with_total_sold')->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setCellValue('A1', 'ID');
        $sheet->setCellValue('B1', 'Sub Category Name');
        $sheet->setCellValue('C1', 'Main Category Name');

        $row = 2;
        foreach ($BestCategories as $BestCategory) {
            $sheet->setCellValue('A' . $row, $BestCategory->id);
            $sheet->setCellValue('B' . $row, $BestCategory->sub_category_name);
            $sheet->setCellValue('C' . $row, $BestCategory->main_category_name);
            $row++;
        }

        //توليد ملف الاكسل و حفظه في مجلد
        $filePath = storage_path('app/public/Best_Categories.xlsx');
        $writer = new Xlsx($spreadsheet);
        $writer->save($filePath);

        //تحميل الملف مباشرة 
        $response = new StreamedResponse(function () use ($filePath) {
            readfile($filePath);
        });

        $response->headers->set('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $response->headers->set('Content-Disposition', 'attachment; filename="Best_Categories.xlsx"');

        return $response;
    }

    /*
     * Export best selling products report and save it on storeg.
     */
    public function bestSellingProductsExport()
    {
        $bestSellingProducts = Product::bestSelling('product_with_total_sold')->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setCellValue('A1', 'ID');
        $sheet->setCellValue('B1', 'Name');
        $sheet->setCellValue('C1', 'Description');
        $sheet->setCellValue('D1', 'Price');
        $sheet->setCellValue('E1', 'Sub Category Name');
        $sheet->setCellValue('F1', 'MAin Category Name');
        $sheet->setCellValue('G1', 'Total Sold');

        $row = 2;
        foreach ($bestSellingProducts as $bestSellingProduct) {
            $sheet->setCellValue('A' . $row, $bestSellingProduct->id);
            $sheet->setCellValue('B' . $row, $bestSellingProduct->name);
            $sheet->setCellValue('C' . $row, $bestSellingProduct->description);
            $sheet->setCellValue('D' . $row, $bestSellingProduct->price);
            $sheet->setCellValue('E' . $row, $bestSellingProduct->sub_category_name);
            $sheet->setCellValue('F' . $row, $bestSellingProduct->main_category_name);
            $sheet->setCellValue('G' . $row, $bestSellingProduct->total_sold);
            $row++;
        }

        //توليد ملف الاكسل و حفظه في مجلد
        $filePath = storage_path('app/public/Best_Selling_Products.xlsx');
        $writer = new Xlsx($spreadsheet);
        $writer->save($filePath);

        //تحميل الملف مباشرة 
        $response = new StreamedResponse(function () use ($filePath) {
            readfile($filePath);
        });

        $response->headers->set('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $response->headers->set('Content-Disposition', 'attachment; filename="Best_Selling_Products.xlsx"');

        return $response;
    }

    /*
     * Export products low on stock report and save it on storeg.
     */
    public function productsLowOnStockExport()
    {
        $LowOnStockproducts = Product::lowStock()->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setCellValue('A1', 'ID');
        $sheet->setCellValue('B1', 'Name');
        $sheet->setCellValue('C1', 'Description');
        $sheet->setCellValue('D1', 'Price');
        $sheet->setCellValue('E1', 'Quantity');
        $sheet->setCellValue('F1', 'Sub Category Name');
        $sheet->setCellValue('G1', 'MAin Category Name');
        $sheet->setCellValue('H1', 'average rating');

        $row = 2;
        foreach ($LowOnStockproducts as $LowOnStockproduct) {
            $sheet->setCellValue('A' . $row, $LowOnStockproduct->id);
            $sheet->setCellValue('B' . $row, $LowOnStockproduct->name);
            $sheet->setCellValue('C' . $row, $LowOnStockproduct->description);
            $sheet->setCellValue('D' . $row, $LowOnStockproduct->price);
            $sheet->setCellValue('E' . $row, $LowOnStockproduct->product_quantity);
            $sheet->setCellValue('F' . $row, $LowOnStockproduct->subCategory->sub_category_name);
            $sheet->setCellValue('G' . $row, $LowOnStockproduct->mainCategory->main_category_name);
            $sheet->setCellValue('H' . $row, $LowOnStockproduct->averageRating() ?? 0);
            $row++;
        }

        //توليد ملف الاكسل و حفظه في مجلد
        $filePath = storage_path('app/public/Low_On_Stock_Products.xlsx');
        $writer = new Xlsx($spreadsheet);
        $writer->save($filePath);

        //تحميل الملف مباشرة 
        $response = new StreamedResponse(function () use ($filePath) {
            readfile($filePath);
        });

        $response->headers->set('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $response->headers->set('Content-Disposition', 'attachment; filename="Low_On_Stock_Products.xlsx"');

        return $response;
    }

    /*
     * Export orders late to deliver report and save it on storeg.
     */
    public function ordersLateToDeliverExport()
    {
        $sevenDaysAgo = Carbon::now()->subDays(7); // Create the current date and subtract 7 days from it

        $lating_orders = Order::where('status', 'shipped')->where('created_at', '<=', $sevenDaysAgo)->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setCellValue('A1', 'ID');
        $sheet->setCellValue('B1', 'Shipped Address');
        $sheet->setCellValue('C1', 'Status');
        $sheet->setCellValue('D1', 'Total Price');
        $sheet->setCellValue('E1', 'Created At');

        $row = 2;
        foreach ($lating_orders as $lating_order) {
            $sheet->setCellValue('A' . $row, $lating_order->id);
            $sheet->setCellValue('B' . $row, $lating_order->shipped_address);
            $sheet->setCellValue('C' . $row, $lating_order->status);
            $sheet->setCellValue('D' . $row, $lating_order->total_price);
            $sheet->setCellValue('E' . $row, Carbon::parse($lating_order->created_at)->format('Y M d, H:i:s'));
            $row++;
        }

        //توليد ملف الاكسل و حفظه في مجلد
        $filePath = storage_path('app/public/orders_Late_To_Deliver.xlsx');
        $writer = new Xlsx($spreadsheet);
        $writer->save($filePath);

        //تحميل الملف مباشرة 
        $response = new StreamedResponse(function () use ($filePath) {
            readfile($filePath);
        });

        $response->headers->set('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $response->headers->set('Content-Disposition', 'attachment; filename="orders_Late_To_Deliver.xlsx"');

        return $response;
    }

    /*
     * Export products never been sold report and save it on storeg.
     */
    public function productsNeverBeenSoldExport()
    {
        $unsoldProducts = Product::neverBeenSold()->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setCellValue('A1', 'ID');
        $sheet->setCellValue('B1', 'Name');
        $sheet->setCellValue('C1', 'Description');
        $sheet->setCellValue('D1', 'Price');
        $sheet->setCellValue('E1', 'Quantity');
                $sheet->setCellValue('F1', 'Sub Category Name');
        $sheet->setCellValue('G1', 'MAin Category Name');
        $sheet->setCellValue('H1', 'average rating');

        $row = 2;
        foreach ($unsoldProducts as $unsoldProduct) {
            $sheet->setCellValue('A' . $row, $unsoldProduct->id);
            $sheet->setCellValue('B' . $row, $unsoldProduct->name);
            $sheet->setCellValue('C' . $row, $unsoldProduct->description);
            $sheet->setCellValue('D' . $row, $unsoldProduct->price);
            $sheet->setCellValue('E' . $row, $unsoldProduct->product_quantity);
                       $sheet->setCellValue('F' . $row, $unsoldProduct->subCategory->sub_category_name);
            $sheet->setCellValue('G' . $row, $unsoldProduct->mainCategory->main_category_name);
            $sheet->setCellValue('H' . $row, $unsoldProduct->averageRating() ?? 0);
            $row++;
        }


        //توليد ملف الاكسل و حفظه في مجلد
        $filePath = storage_path('app/public/unsold_Products.xlsx');
        $writer = new Xlsx($spreadsheet);
        $writer->save($filePath);

        //تحميل الملف مباشرة 
        $response = new StreamedResponse(function () use ($filePath) {
            readfile($filePath);
        });

        $response->headers->set('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $response->headers->set('Content-Disposition', 'attachment; filename="unsold_Products.xlsx"');

        return $response;
    }

    /*
     * Export products remaining in cart report and save it on storeg.
     */
    public function productsRemainingInCartsExport()
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
                        ->where('created_at', '<=', Carbon::now()->subMonths(2))
                        ->with([
                            'product' => function ($q) {
                                $q->select('id', 'name');
                            }
                        ]);
                }
            ])
            ->select('id', 'user_id')
            ->get();
            

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setCellValue('A1', 'Cart ID');
        $sheet->setCellValue('B1', 'User ID');
        $sheet->setCellValue('C1', 'Product ID');
        $sheet->setCellValue('D1', 'Product Name');
        $sheet->setCellValue('E1', 'Created At');

        $row = 2;
        foreach ($products_remaining as $product_remaining) {
            $sheet->setCellValue('A' . $row, $product_remaining->cart_id);
            $sheet->setCellValue('B' . $row, $product_remaining->user_id);
            $sheet->setCellValue('C' . $row, $product_remaining->product_id);
            $sheet->setCellValue('D' . $row, $product_remaining->product->name);
            $sheet->setCellValue('E' . $row, $product_remaining->created_at);
            $row++;
        }


        //توليد ملف الاكسل و حفظه في مجلد
        $filePath = storage_path('app/public/products_remaining.xlsx');
        $writer = new Xlsx($spreadsheet);
        $writer->save($filePath);

        //تحميل الملف مباشرة 
        $response = new StreamedResponse(function () use ($filePath) {
            readfile($filePath);
        });

        $response->headers->set('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $response->headers->set('Content-Disposition', 'attachment; filename="products_remaining.xlsx"');

        return $response;
    }

    /*
     * Export countries with highest orders report and save it on storeg.
     */
    public function countriesWithHighestOrdersExport(TopCountryRequest $request, int $country = 5)
    {
        $data = $request->validationData();

        $topCountries = Order::with('zone.city.country')
            ->when(isset($data['start_date']), function ($q) use ($data) {
                return $q->whereDate('created_at', '>=', $data['start_date']);
            })
            ->when(isset($data['end_date']), function ($q) use ($data) {
                return $q->whereDate('created_at', '<=', $data['end_date']);
            })
            ->get()
            ->groupBy(fn($order) => $order->zone->city->country->name)
            ->map(fn($orders, $countryName) => [
                'country_name' => $countryName,
                'total_orders' => $orders->count(),
            ])
            ->sortByDesc('total_orders')
            ->take($country)
            ->values();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // كتابة العناوين في الصف الأول
        $sheet->setCellValue('A1', 'Country');
        $sheet->setCellValue('B1', 'Total Orders');

        // تعبئة البيانات
        $row = 2;
        foreach ($topCountries as $topCountry) {
            $sheet->setCellValue('A' . $row, $topCountry['country_name']);
            $sheet->setCellValue('B' . $row, $topCountry['total_orders']);
            $row++;
        }

        $filePath = storage_path('app/public/countries_With_Highest_Orders.xlsx');
        $writer = new Xlsx($spreadsheet);
        $writer->save($filePath);

        $response = new StreamedResponse(function () use ($filePath) {
            readfile($filePath);
        });

        $response->headers->set('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $response->headers->set('Content-Disposition', 'attachment; filename="countries_With_Highest_Orders.xlsx"');

        return $response;
    }
}
