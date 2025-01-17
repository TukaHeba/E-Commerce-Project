<?php

namespace App\Http\Controllers\Payment;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\Payment\PaymentService;

class PaymentController extends Controller
{
    protected $PaymentService;
    public function __construct(PaymentService $PaymentService)
    {
        $this->PaymentService = $PaymentService;
    }

    public function createPaymentMethod(Request $request)
    {
        $validatedData = $request->validate(['token' => 'required']);
        $paymentMethodID = $this->PaymentService->PaymentMethod($validatedData);
    
     return self::success($paymentMethodID, 'Payment method created successfully.');
    }

   
}
