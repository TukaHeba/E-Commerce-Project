<?php

namespace App\Services\Payment;

use Stripe\Stripe;
use Stripe\PaymentMethod;

class PaymentService
{

    public function PaymentMethod($validatedRequest)
    {
        try {
            // Set the Stripe API key
            Stripe::setApiKey(env('STRIPE_SECRET_KEY'));

            // Create the payment method
            $paymentMethod = PaymentMethod::create([
                'type' => 'card',
                'card' => [
                    'token' => $validatedRequest['token'],
                ],
            ]);

            // Save Payment Method ID in Session
            session(['payment_method_id' => $paymentMethod->id]);

            return $paymentMethod->id;
        } catch (\Exception $e) {
            // Handle errors
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function stripePayment($cartData)
    {
        try {
            // Set the Stripe API key
            Stripe::setApiKey(env('STRIPE_SECRET_KEY'));

            // Get the payment method ID from session
            $paymentMethodId = session('payment_method_id');

            // Charge the customer
            $charge = \Stripe\Charge::create([
                'amount' =>  $cartData['total_price'] * 100, // Amount in cents
                'currency' => 'usd',
                'description' => 'Example charge',
                'source' => $paymentMethodId,
            ]);

            // Clear the payment method ID from session
            session()->forget('payment_method_id');

            // Respond with success and charge details
            return response()->json([
                'success' => true,
                'charge_id' => $charge->id,
                'message' => 'Payment successful.',
            ], 200);
        } catch (\Exception $e) {
            // Handle errors
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while processing your payment.',
            ], 500);
        }
    }
}
