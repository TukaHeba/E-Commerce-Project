<?php
namespace App\Exports;

use App\Models\Product\Product;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class LowStockExport implements FromCollection, WithHeadings
{
    /**
     * Get the collection of products with low stock.
     */
    public function collection()
    {
        return Product::lowStock()->get(columns: ['id', 'name', 'product_quantity']);
    }

    /**
     * Define the headings for the Excel file.
     */
    public function headings(): array
    {
        return ['ID', 'Name', 'Quantity'];
    }
}
