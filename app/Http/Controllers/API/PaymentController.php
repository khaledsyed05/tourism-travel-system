<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Payment;
use App\Services\PaymentService;
use Illuminate\Http\Request;
use Exception;
use Stripe\PaymentIntent;

class PaymentController extends Controller
{
    protected $paymentService;

    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    public function createPaymentIntent(Request $request)
    {
        $request->validate([
            'booking_id' => 'required|exists:bookings,id',
        ]);

        $booking = Booking::findOrFail($request->booking_id);
        
        try {
            $paymentIntent = $this->paymentService->createPaymentIntent($booking);
            return response()->json($paymentIntent);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function confirmPayment(Request $request)
    {
        $request->validate([
            'payment_intent_id' => 'required|string',
            'payment_method_id' => 'required|string',
        ]);

        try {
            $result = $this->paymentService->confirmPayment(
                $request->payment_intent_id,
                $request->payment_method_id
            );

            if (isset($result['requiresAction'])) {
                return response()->json([
                    'requires_action' => true,
                    'payment_intent_client_secret' => $result['paymentIntentClientSecret']
                ]);
            }

            return response()->json([
                'message' => 'Payment confirmed successfully',
                'payment' => $result,
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
