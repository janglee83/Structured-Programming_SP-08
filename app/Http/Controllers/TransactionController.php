<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreatePaymentRequest;
use App\Models\Invoice;
use App\Models\Transaction;
use App\Repositories\InvoiceRepository;
use App\Repositories\TransactionRepository;
use App\Services\VNPAYService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TransactionController extends ApiController
{
    private $transactionRepository;
    private $invoiceRepository;

    public function __construct(TransactionRepository $transactionRepository, InvoiceRepository $invoiceRepository)
    {
        $this->transactionRepository = $transactionRepository;
        $this->invoiceRepository = $invoiceRepository;
    }

    private function createTransaction($data)
    {
        $trans = $this->transactionRepository->create($data);
        $trans = $this->transactionRepository->update([
            'payment_code' => Transaction::generateCode($trans['id'], $trans['created_at']),
        ], $trans['id']);

        return $trans;
    }
    public function processPayment(CreatePaymentRequest $request)
    {
        $money = abs($request->money);
        $method = empty($request->payment_method) ? "" : $request->payment_method;

        if ($method != "Shipcode") {
            $trans_data = [
                'customer_id' => $request->customer_id,
                'status' => 'pending',
                "order_id" => $request->order_id,
                'method' => $method,
                'money' => $money,
                'payment_date' => now()
            ];

            $trans = $this->createTransaction($trans_data);
        }

        $invoice = $this->invoiceRepository->create([
            "order_id" => $request->order_id,
            "invoice_code" => "123",
            "transaction_id" => $trans['id'],
            "total" => $money,
            "status" => "new",
        ]);

        $invoice = $this->invoiceRepository->update([
            'invoice_code' => Invoice::generateCode($trans['id']),
        ], $invoice['id']);

        if ($method === "vnpayqr" || $method === "vnpay" || $method === "atm") {
            $url = VNPAYService::create_payment($trans->payment_code, $money, "vnpayqr");
        } else if ($method === "shipcode") {
            // TODO:
//            Http::post('SP_01:orderManagement/chuathanhtoan', [
//                'status' => ,
//            ]);
            $url = "http://localhost:8000/api/invoices/" . $invoice['id'] . "/status";
        } else
            $url = "http://localhost:8000/";

        return $this->successResponse(["url" => $url], "Successfully!");
    }

    public function refund(Request $request)
    {

    }

    public function getTransactions(Request $request)
    {
        $filter = $request->only('created_at', 'payment_code', 'status', 'payment_date', 'method', 'type');

        try {
            $orderData = $this->transactionRepository->getTransactions($filter);

            return $this->successResponse($orderData, "Successfully!");
        } catch (\Exception $exception) {
            Log::error("[ERROR]" . $exception->getMessage());
            return $this->errorResponse([], 'Server error', 500);
        }
    }

    public function statistic(Request $request)
    {
        try {
            $transaction = $this->transactionRepository->getStatistic($request);
            $payment_method = $this->transactionRepository->getStatisticByPaymentMethod($request);

            return $this->successResponse([
                'transaction' => $transaction,
                'method' => $payment_method
            ], "Successfully!");
        } catch (\Exception $exception) {
            Log::error("[ERROR]" . $exception->getMessage());
            return $this->errorResponse([], 'Server error', 500);
        }
    }
}
