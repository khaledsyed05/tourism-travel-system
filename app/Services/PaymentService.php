<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\Payment;
use Stripe\Exception\ApiErrorException;
use Stripe\Stripe;
use Stripe\PaymentIntent;
use Exception;
use Stripe\PaymentMethod;

class PaymentService
{
    public function __construct()
    {
        Stripe::setApiKey(config('services.stripe.secret'));
    }

    public function createPaymentIntent(Booking $booking)
    {
        try {
            $paymentIntent = PaymentIntent::create([
                'amount' => $booking->total_price * 100, // amount in cents
                'currency' => 'usd',
                'automatic_payment_methods' => [
                    'enabled' => true,
                    'allow_redirects' => 'never'
                ],
                'metadata' => [
                    'booking_id' => $booking->id
                ]
            ]);

            return [
                'clientSecret' => $paymentIntent->client_secret,
                'paymentIntentId' => $paymentIntent->id
            ];
        } catch (ApiErrorException $e) {
            report($e);
            throw new \Exception('Error creating payment intent: ' . $e->getMessage());
        }
    }

    public function confirmPayment(string $paymentIntentId, string $paymentMethodId)
    {
        try {
            $paymentIntent = PaymentIntent::retrieve($paymentIntentId);
            $paymentIntent->confirm([
                'payment_method' => $paymentMethodId,
            ]);

            if ($paymentIntent->status === 'requires_action') {
                // Handle 3D Secure authentication if needed
                return [
                    'requiresAction' => true,
                    'paymentIntentClientSecret' => $paymentIntent->client_secret
                ];
            }

            if ($paymentIntent->status === 'succeeded') {
                // Payment successful, create payment record
                return $this->createPaymentRecord($paymentIntent);
            }

            // Handle other statuses
            throw new \Exception('Payment failed. Status: ' . $paymentIntent->status);
        } catch (ApiErrorException $e) {
            report($e);
            throw new \Exception('Error confirming payment: ' . $e->getMessage());
        }
    }
    public function createTestPaymentMethod()
{
    try {
        $paymentMethod = PaymentMethod::create([
            'type' => 'card',
            'card' => [
                'number' => '4242424242424242',
                'exp_month' => 12,
                'exp_year' => 2024,
                'cvc' => '314',
            ],
        ]);

        return $paymentMethod->id;
    } catch (ApiErrorException $e) {
        throw new \Exception('Error creating payment method: ' . $e->getMessage());
    }
}

    private function createPaymentRecord($paymentIntent)
    {
        // Create a record in your payments table
        return Payment::create([
            'booking_id' => $paymentIntent->metadata['booking_id'],
            'amount' => $paymentIntent->amount / 100, // Convert back to dollars
            'currency' => $paymentIntent->currency,
            'payment_method' => $paymentIntent->payment_method,
            'status' => $paymentIntent->status,
            'transaction_id' => $paymentIntent->id,
            'payment_details' => json_encode($paymentIntent)
        ]);
    }
}