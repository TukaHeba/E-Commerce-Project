<?php
namespace App\Exports;

use App\Models\Product\Product;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class UnsoldExport implements FromCollection, WithHeadings
{
    /**
     * Get the collection of unsold products .
     */
    public function collection()
    {
        return Product::neverBeenSold()->get(columns: ['id', 'name', 'product_quantity']);
    }

    /**
     * Define the headings for the Excel file.
     */
    public function headings(): array
    {
        return ['ID', 'Name', 'Quantity'];
    }
}
