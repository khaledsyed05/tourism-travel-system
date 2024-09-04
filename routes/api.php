<?php

use App\Http\Controllers\API\PaymentController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

require __DIR__."\AuthApis.php";

require __DIR__."\DashboardApis.php";

require __DIR__."\AdminApis.php";

require __DIR__."\ClientSideApis.php";

Route::post('/create-payment-intent', [PaymentController::class, 'createPaymentIntent']);
Route::post('/confirm-payment', [PaymentController::class, 'confirmPayment']);

