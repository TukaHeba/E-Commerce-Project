<?php

namespace App\Exports;

use App\Models\Product\Product;
use App\Services\Report\ReportService;
use Maatwebsite\Excel\Concerns\FromCollection;

class LateProductsExport implements FromCollection
{
    protected $reportService ;
    public function __construct(ReportService $reportService){
        $this->reportService = $reportService;
    }
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $lateProducts = $this->reportService->getOrdersLateToDeliver();
        return collect($lateProducts->items()) ;
    }
    public function headings(){
        return [];
    }
}
