<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Order\Order;
use App\Models\Product\Product;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ExportController extends Controller
{
    public function bestCategoriesExport()
    {
        $BestCategories = Product::Selling()->get();

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

        $response = new StreamedResponse(function () use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        });

        $response->headers->set('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $response->headers->set('Content-Disposition', 'attachment; filename="Best_Categories.xlsx"');

        return $response;
    }



    public function bestSellingProductsExport()
    {
        $bestSellingProducts = Product::bestSelling()->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setCellValue('A1', 'ID');
        $sheet->setCellValue('B1', 'Name');
        $sheet->setCellValue('C1', 'Description');
        $sheet->setCellValue('D1', 'Price');
        $sheet->setCellValue('E1', 'Category Name');
        $sheet->setCellValue('F1', 'Total Sold');

        $row = 2;
        foreach ($bestSellingProducts as $bestSellingProduct) {
            $sheet->setCellValue('A' . $row, $bestSellingProduct->id);
            $sheet->setCellValue('B' . $row, $bestSellingProduct->name);
            $sheet->setCellValue('C' . $row, $bestSellingProduct->description);
            $sheet->setCellValue('D' . $row, $bestSellingProduct->price);
            $sheet->setCellValue('E' . $row, $bestSellingProduct->category_name);
            $sheet->setCellValue('F' . $row, $bestSellingProduct->total_sold);
            $row++;
        }

        $response = new StreamedResponse(function () use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        });

        $response->headers->set('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $response->headers->set('Content-Disposition', 'attachment; filename="Best_Selling_Products.xlsx"');

        return $response;
    }



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

        $row = 2;
        foreach ($LowOnStockproducts as $LowOnStockproduct) {
            $sheet->setCellValue('A' . $row, $LowOnStockproduct->id);
            $sheet->setCellValue('B' . $row, $LowOnStockproduct->name);
            $sheet->setCellValue('C' . $row, $LowOnStockproduct->description);
            $sheet->setCellValue('D' . $row, $LowOnStockproduct->price);
            $sheet->setCellValue('E' . $row, $LowOnStockproduct->product_quantity);
            $row++;
        }

        $response = new StreamedResponse(function () use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        });

        $response->headers->set('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $response->headers->set('Content-Disposition', 'attachment; filename="Low_On_Stock_Products.xlsx"');

        return $response;
    }


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

        $response = new StreamedResponse(function () use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        });

        $response->headers->set('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $response->headers->set('Content-Disposition', 'attachment; filename="lating_orders.xlsx"');

        return $response;
    }


    public function productsNeverBeenSoldExport()
    {
        $unsoldProducts = Product::whereDoesntHave('orderItems')->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setCellValue('A1', 'ID');
        $sheet->setCellValue('B1', 'Name');
        $sheet->setCellValue('C1', 'Description');
        $sheet->setCellValue('D1', 'Price');
        $sheet->setCellValue('E1', 'Quantity');

        $row = 2;
        foreach ($unsoldProducts as $unsoldProduct) {
            $sheet->setCellValue('A' . $row, $unsoldProduct->id);
            $sheet->setCellValue('B' . $row, $unsoldProduct->name);
            $sheet->setCellValue('C' . $row, $unsoldProduct->description);
            $sheet->setCellValue('D' . $row, $unsoldProduct->price);
            $sheet->setCellValue('E' . $row, $unsoldProduct->product_quantity);
            $row++;
        }

        $response = new StreamedResponse(function () use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        });

        $response->headers->set('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $response->headers->set('Content-Disposition', 'attachment; filename="unsold_Products.xlsx"');

        return $response;
    }


    public function countriesWithHighestOrdersExport()
    {
        $countriesWithHighestOrders = Order::selectRaw('addresses.country, COUNT(orders.id) as total_orders')
                                            ->join('addresses', 'orders.address_id', '=', 'addresses.id')
                                            ->groupBy('addresses.country')
                                            ->orderByDesc('total_orders')
                                            ->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setCellValue('A1', 'Country');
        $sheet->setCellValue('B1', 'Total Orders');

        $row = 2;
        foreach ($countriesWithHighestOrders as $countriesWithHighestOrder) {
            $sheet->setCellValue('A' . $row, $countriesWithHighestOrder->name);
            $sheet->setCellValue('B' . $row, $countriesWithHighestOrder->description);
            $row++;
        }

        $response = new StreamedResponse(function () use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        });

        $response->headers->set('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $response->headers->set('Content-Disposition', 'attachment; filename="countries_With_Highest_Orders.xlsx"');

        return $response;
    }
}
