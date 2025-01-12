<?php

namespace App\Services\Export;

use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use App\Services\Report\ReportService;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;


class ExportService
{
    protected ReportService $ReportService;

    public function __construct(ReportService $ReportService)
    {
        $this->ReportService = $ReportService;
    }

    /*
     * Export best categories report.
     * return Excel sheet
     */
    public function bestCategoriesExport()
    {
        $BestCategories = $this->ReportService->getBestSellingCategories();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setCellValue('A1', 'Sub Category Name');
        $sheet->setCellValue('B1', 'Main Category Name');
        $sheet->setCellValue('C1', 'Total Sold');


        $row = 2;
        foreach ($BestCategories as $BestCategory) {
            $sheet->setCellValue('A' . $row, $BestCategory->sub_category_name);
            $sheet->setCellValue('B' . $row, $BestCategory->main_category_name);
            $sheet->setCellValue('C' . $row, $BestCategory->total_sold);
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

    /*
     * Export best selling products report.
     * return Excel sheet
     */
    public function bestSellingProductsExport()
    {

        $bestSellingProducts = $this->ReportService->getBestSellingProducts();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setCellValue('A1', 'ID');
        $sheet->setCellValue('B1', 'Name');
        $sheet->setCellValue('C1', 'Description');
        $sheet->setCellValue('D1', 'Price');
        $sheet->setCellValue('E1', 'Sub Category Name');
        $sheet->setCellValue('F1', 'Main Category Name');
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

        $response = new StreamedResponse(function () use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        });

        $response->headers->set('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $response->headers->set('Content-Disposition', 'attachment; filename="Best_Selling_Products.xlsx"');

        return $response;
    }

    /*
     * Export products low on stock report.
     * return Excel sheet
     */
    public function productsLowOnStockExport()
    {
        $LowOnStockproducts = $this->ReportService->getProductsLowOnStock();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setCellValue('A1', 'ID');
        $sheet->setCellValue('B1', 'Name');
        $sheet->setCellValue('C1', 'Description');
        $sheet->setCellValue('D1', 'Price');
        $sheet->setCellValue('E1', 'Quantity');
        $sheet->setCellValue('F1', 'average rating');

        $row = 2;
        foreach ($LowOnStockproducts as $LowOnStockproduct) {
            $sheet->setCellValue('A' . $row, $LowOnStockproduct->id);
            $sheet->setCellValue('B' . $row, $LowOnStockproduct->name);
            $sheet->setCellValue('C' . $row, $LowOnStockproduct->description);
            $sheet->setCellValue('D' . $row, $LowOnStockproduct->price);
            $sheet->setCellValue('E' . $row, $LowOnStockproduct->product_quantity);
            $sheet->setCellValue('F' . $row, $LowOnStockproduct->averageRating() ?? 0);
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

    /*
     * Export orders late to deliver report.
     * return Excel sheet
     */
    public function ordersLateToDeliverExport()
    {
        $lating_orders = $this->ReportService->getOrdersLateToDeliver();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setCellValue('A1', 'ID');
        $sheet->setCellValue('B1', 'Customer Name');
        $sheet->setCellValue('C1', 'Customer Email');
        $sheet->setCellValue('D1', 'Customer Phone');
        $sheet->setCellValue('E1', 'Zone');
        $sheet->setCellValue('F1', 'City');
        $sheet->setCellValue('G1', 'Postal Code');
        $sheet->setCellValue('H1', 'Status');
        $sheet->setCellValue('I1', 'Total Price');
        $sheet->setCellValue('J1', 'Order Number');

        $row = 2;
        foreach ($lating_orders as $lating_order) {
            $sheet->setCellValue('A' . $row, $lating_order->id);
            $sheet->setCellValue('B' . $row, $lating_order->user->full_name);
            $sheet->setCellValue('C' . $row, $lating_order->user->email);
            $sheet->setCellValue('D' . $row, $lating_order->user->phone);
            $sheet->setCellValue('E' . $row, $lating_order->zone->name);
            $sheet->setCellValue('F' . $row, $lating_order->zone->city->name);
            $sheet->setCellValue('G' . $row, $lating_order->postal_code);
            $sheet->setCellValue('H' . $row, $lating_order->status);
            $sheet->setCellValue('I' . $row, $lating_order->total_price);
            $sheet->setCellValue('J' . $row, $lating_order->order_number);
            $row++;
        }

        $response = new StreamedResponse(function () use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        });

        $response->headers->set('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $response->headers->set('Content-Disposition', 'attachment; filename="orders_Late_To_Deliver.xlsx"');

        return $response;
    }

    /*
     * Export products never been sold report.
     * return Excel sheet
     */
    public function productsNeverBeenSoldExport()
    {
        $unsoldProducts = $this->ReportService->getProductsNeverBeenSold();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setCellValue('A1', 'ID');
        $sheet->setCellValue('B1', 'Name');
        $sheet->setCellValue('C1', 'Description');
        $sheet->setCellValue('D1', 'Price');
        $sheet->setCellValue('E1', 'Quantity');
        $sheet->setCellValue('F1', 'average rating');

        $row = 2;
        foreach ($unsoldProducts as $unsoldProduct) {
            $sheet->setCellValue('A' . $row, $unsoldProduct->id);
            $sheet->setCellValue('B' . $row, $unsoldProduct->name);
            $sheet->setCellValue('C' . $row, $unsoldProduct->description);
            $sheet->setCellValue('D' . $row, $unsoldProduct->price);
            $sheet->setCellValue('E' . $row, $unsoldProduct->product_quantity);
            $sheet->setCellValue('F' . $row, $unsoldProduct->averageRating() ?? 0);
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

    /**
     * Export products remaining in cart report.
     * @return string
     */
    public function productsRemainingInCartsExport(): string
    {
        $products_remaining = $this->ReportService->getProductsRemainingInCarts();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setCellValue('A1', 'Cart ID');
        $sheet->setCellValue('B1', 'User ID');
        $sheet->setCellValue('C1', 'Product ID');
        $sheet->setCellValue('D1', 'Product Name');
        $sheet->setCellValue('E1', 'Product Description');
        $sheet->setCellValue('F1', 'Quantity');

        $row = 2;
        foreach ($products_remaining as $product_remaining) {
            foreach ($product_remaining->toArray()['cart_items'] as $cart_item) {
                $sheet->setCellValue('A' . $row, $product_remaining->id);
                $sheet->setCellValue('B' . $row, $product_remaining->user_id);
                $sheet->setCellValue('C' . $row, $cart_item['product_id']);
                $sheet->setCellValue('D' . $row, $cart_item['product']['name']);
                $sheet->setCellValue('E' . $row, $cart_item['product']['description']);
                $sheet->setCellValue('F' . $row, $cart_item['quantity']);
                $row++;
            }
        }

        $response = new StreamedResponse(function () use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        });

        $response->headers->set('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $response->headers->set('Content-Disposition', 'attachment; filename="products-remaining-in-carts.xlsx"');

        return $response;
    }

    /*
     * Export countries with highest orders report.
     * return Excel sheet
     */
    public function countriesWithHighestOrdersExport($data=[], $country=5)
    {
        $topCountries = $this->ReportService->getCountriesWithHighestOrders($data,$country);


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

        $response = new StreamedResponse(function () use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        });

        $response->headers->set('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $response->headers->set('Content-Disposition', 'attachment; filename="countries_With_Highest_Orders.xlsx"');

        return $response;
    }

    /*
     * Export best categories report and save it on storeg.
     * return filePath
     */
    public function bestCategoriesExportStorage()
    {
        $BestCategories = $this->ReportService->getBestSellingCategories();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setCellValue('A1', 'Sub Category Name');
        $sheet->setCellValue('B1', 'Main Category Name');
        $sheet->setCellValue('C1', 'Total Sold');


        $row = 2;
        foreach ($BestCategories as $BestCategory) {
            $sheet->setCellValue('A' . $row, $BestCategory->sub_category_name);
            $sheet->setCellValue('B' . $row, $BestCategory->main_category_name);
            $sheet->setCellValue('C' . $row, $BestCategory->total_sold);
            $row++;
        }

        $fileName = 'Best_Categories.xlsx'. now()->format('Y-m-d') . '.xlsx';
        $filePath = 'reports/' . $fileName;
        $writer = new Xlsx($spreadsheet);
        $writer->save(Storage::disk('public')->path($filePath));

        return $filePath;
    }

    /*
     * Export best selling products report and save it on storeg.
     * return filePath
     */
    public function bestSellingProductsExportStorage()
    {

        $bestSellingProducts = $this->ReportService->getBestSellingProducts();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setCellValue('A1', 'ID');
        $sheet->setCellValue('B1', 'Name');
        $sheet->setCellValue('C1', 'Description');
        $sheet->setCellValue('D1', 'Price');
        $sheet->setCellValue('E1', 'Sub Category Name');
        $sheet->setCellValue('F1', 'Main Category Name');
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


        $fileName = 'Best_Selling_Products.xlsx';
        $filePath = 'reports/' . $fileName;
        $writer = new Xlsx($spreadsheet);
        $writer->save(Storage::disk('public')->path($filePath));

        return $filePath;
    }

    /*
     * Export products low on stock report and save it on storeg.
     * return filePath
     */
    public function productsLowOnStockExportStorage()
    {
        $LowOnStockproducts = $this->ReportService->getProductsLowOnStock();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setCellValue('A1', 'ID');
        $sheet->setCellValue('B1', 'Name');
        $sheet->setCellValue('C1', 'Description');
        $sheet->setCellValue('D1', 'Price');
        $sheet->setCellValue('E1', 'Quantity');
        $sheet->setCellValue('F1', 'average rating');

        $row = 2;
        foreach ($LowOnStockproducts as $LowOnStockproduct) {
            $sheet->setCellValue('A' . $row, $LowOnStockproduct->id);
            $sheet->setCellValue('B' . $row, $LowOnStockproduct->name);
            $sheet->setCellValue('C' . $row, $LowOnStockproduct->description);
            $sheet->setCellValue('D' . $row, $LowOnStockproduct->price);
            $sheet->setCellValue('E' . $row, $LowOnStockproduct->product_quantity);
            $sheet->setCellValue('F' . $row, $LowOnStockproduct->averageRating() ?? 0);
            $row++;
        }

        $fileName = 'Low_On_Stock_Products.xlsx';
        $filePath = 'reports/' . $fileName;
        $writer = new Xlsx($spreadsheet);
        $writer->save(Storage::disk('public')->path($filePath));

        return $filePath; // إرجاع المسار النسبي للملف
    }

    /*
     * Export orders late to deliver report and save it on storeg.
     * return filePath
     */
    public function ordersLateToDeliverExportStorage()
    {
        $lating_orders = $this->ReportService->getOrdersLateToDeliver();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();


        $sheet->setCellValue('A1', 'ID');
        $sheet->setCellValue('B1', 'Customer Name');
        $sheet->setCellValue('C1', 'Customer Email');
        $sheet->setCellValue('D1', 'Customer Phone');
        $sheet->setCellValue('E1', 'Zone');
        $sheet->setCellValue('F1', 'City');
        $sheet->setCellValue('G1', 'Postal Code');
        $sheet->setCellValue('H1', 'Status');
        $sheet->setCellValue('I1', 'Total Price');
        $sheet->setCellValue('J1', 'Order Number');

        $row = 2;
        foreach ($lating_orders as $lating_order) {
            $sheet->setCellValue('A' . $row, $lating_order->id);
            $sheet->setCellValue('B' . $row, $lating_order->user->full_name);
            $sheet->setCellValue('C' . $row, $lating_order->user->email);
            $sheet->setCellValue('D' . $row, $lating_order->user->phone);
            $sheet->setCellValue('E' . $row, $lating_order->zone->name);
            $sheet->setCellValue('F' . $row, $lating_order->zone->city->name);
            $sheet->setCellValue('G' . $row, $lating_order->postal_code);
            $sheet->setCellValue('H' . $row, $lating_order->status);
            $sheet->setCellValue('I' . $row, $lating_order->total_price);
            $sheet->setCellValue('J' . $row, $lating_order->order_number);
            $row++;
        }

        $fileName = 'orders_Late_To_Deliver.xlsx';
        $filePath = 'reports/' . $fileName;
        $writer = new Xlsx($spreadsheet);
        $writer->save(Storage::disk('public')->path($filePath));

        return $filePath;
    }

    /*
     * Export products never been sold report and save it on storeg.
     * return filePath
     */
    public function productsNeverBeenSoldExportStorage()
    {
        $unsoldProducts = $this->ReportService->getProductsNeverBeenSold();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setCellValue('A1', 'ID');
        $sheet->setCellValue('B1', 'Name');
        $sheet->setCellValue('C1', 'Description');
        $sheet->setCellValue('D1', 'Price');
        $sheet->setCellValue('E1', 'Quantity');
        $sheet->setCellValue('F1', 'average rating');

        $row = 2;
        foreach ($unsoldProducts as $unsoldProduct) {
            $sheet->setCellValue('A' . $row, $unsoldProduct->id);
            $sheet->setCellValue('B' . $row, $unsoldProduct->name);
            $sheet->setCellValue('C' . $row, $unsoldProduct->description);
            $sheet->setCellValue('D' . $row, $unsoldProduct->price);
            $sheet->setCellValue('E' . $row, $unsoldProduct->product_quantity);
            $sheet->setCellValue('F' . $row, $unsoldProduct->averageRating() ?? 0);
            $row++;
        }


        $fileName = 'unsold_Products.xlsx';
        $filePath = 'reports/' . $fileName;
        // Ensure the directory exists
         if (!Storage::exists('public/reports'))
          {
            Storage::makeDirectory('public/reports');
         }
        $writer = new Xlsx($spreadsheet);
        $writer->save(Storage::disk('public')->path($filePath));

        return $filePath;
    }

    /*
     * Export products remaining in cart report and save it on storeg.
     * return filePath
     */
    public function productsRemainingInCartsExportStorage()
    {
        $products_remaining = $this->ReportService->getProductsRemainingInCarts();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setCellValue('A1', 'Cart ID');
        $sheet->setCellValue('B1', 'User ID');
        $sheet->setCellValue('C1', 'Product ID');
        $sheet->setCellValue('D1', 'Product Name');
        $sheet->setCellValue('E1', 'Product Description');
        $sheet->setCellValue('F1', 'Quantity');

        $row = 2;
        foreach ($products_remaining as $product_remaining) {
            foreach ($product_remaining->toArray()['cart_items'] as $cart_item) {
                $sheet->setCellValue('A' . $row, $product_remaining->id);
                $sheet->setCellValue('B' . $row, $product_remaining->user_id);
                $sheet->setCellValue('C' . $row, $cart_item['product_id']);
                $sheet->setCellValue('D' . $row, $cart_item['product']['name']);
                $sheet->setCellValue('E' . $row, $cart_item['product']['description']);
                $sheet->setCellValue('F' . $row, $cart_item['quantity']);
                $row++;
            }
        }

        $fileName = 'products_remaining.xlsx';
        $filePath = 'reports/' . $fileName;
        $writer = new Xlsx($spreadsheet);
        $writer->save(Storage::disk('public')->path($filePath));

        return $filePath;
    }

    /*
     * Export countries with highest orders report and save it on storeg.
     * return filePath
     */
    public function countriesWithHighestOrdersExportStorage($data=[], $country=5)
    {
        $topCountries = $this->ReportService->getCountriesWithHighestOrders($data,$country);

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

        $fileName = 'countries_With_Highest_Orders.xlsx';
        $filePath = 'reports/' . $fileName;
        $writer = new Xlsx($spreadsheet);
        $writer->save(Storage::disk('public')->path($filePath));

        return $filePath;
    }
}
