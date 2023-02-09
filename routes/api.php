<?php

use App\Http\Controllers\CustomerController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PaymentTypeController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\VNPAYController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::group(['middleware' => 'api'], function () {
//    Route::prefix('/customers')->group(function () {
//        Route::get('/', [CustomerController::class, 'getCustomers']);
//        Route::post('/{customer}', [CustomerController::class, 'store']);
//    });
//
//    Route::prefix('/orders')->group(function () {
//        Route::get('/', [OrderController::class, 'getOrders']);
//    });
//
//    Route::prefix('/invoices')->group(function () {
//        Route::get('/', [InvoiceController::class, 'getInvoices']);
//        Route::get('/{invoice_id}/status', [InvoiceController::class, 'status']);
//    });

    Route::prefix('/transactions')->group(function () {
//        Route::get("/", [TransactionController::class, "getTransactions"]);
        Route::get("/by-payment-code", [TransactionController::class, "getTransactionByPaymentCode"]);
        Route::get("/by-order", [TransactionController::class, "getTransactionByOrder"]);

        Route::post("/", [TransactionController::class, "processPayment"]);
        Route::post("/refund", [TransactionController::class, "refund"]);

        Route::post("/{transaction}/status", [TransactionController::class, "changeStatus"]);
    });

    Route::group(["prefix" => "vnpay"], function () {
        Route::get('/return', [VNPAYController::class, 'return']);
        Route::get('/ipn', [VNPAYController::class, 'ipn']);
    });

    Route::prefix('/admin')->group(function () {
        Route::prefix("/transactions")->group(function () {
            Route::get("/", [TransactionController::class, "getTransactions"]);
            Route::get("/statistic", [TransactionController::class, "statistic"]);
            Route::get("/statistic-by-month", [TransactionController::class, "statisticByMonth"]);
            Route::get("/statistic-pay-and-refund", [TransactionController::class, "statisticPayAndRefund"]);
            Route::get("/payment-method", [PaymentTypeController::class, "getPaymentTypeList"]);
            Route::put("/payment-change-status", [PaymentTypeController::class, "setPaymentStatus"]);
        });
    });
});
