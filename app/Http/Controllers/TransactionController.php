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
        $orderId = $request->order_id;

        $trans_data = [
            'customer_id' => $request->customer_id,
            'status' => 'pending',
            "order_id" => $orderId,
            'method' => $method,
            'money' => $money,
            'payment_date' => now()
        ];

        $trans = $this->createTransaction($trans_data);

        // TODO: tạo hóa đơn
//        $invoice = $this->invoiceRepository->create([
//            "order_id" => $request->order_id,
//            "invoice_code" => "123",
//            "transaction_id" => $trans['id'],
//            "total" => $money,
//            "status" => "new",
//        ]);
//
//        $invoice = $this->invoiceRepository->update([
//            'invoice_code' => Invoice::generateCode($trans['id']),
//        ], $invoice['id']);

        if ($method === "vnpay") {
            $url = VNPAYService::create_payment($trans->payment_code, $money, "VNPAYQR");
        } else if ($method === "shipcode") {
            // TODO: Đổi trạng thái hóa đơn
            Http::post('SP_01:api/'. $orderId .'/capnhattrangthai', [
                'status' => "unpaid"
            ]);
            $url = config('frontend.url') . "/invoices/" . $orderId . "/status";
        } else
//            INTCARD
            $url = VNPAYService::create_payment($trans->payment_code, $money, "VNBANK");

        return $this->successResponse(["url" => $url], "Successfully!");
    }

    public function refund(Request $request)
    {
        $money = abs($request->money);
        $paymentCode = $request->payment_code;
        $orderId = $request->order_id;
        // TODO:: tìm payment_code

        $trans = $this->transactionRepository->findByPaymentCode($paymentCode);

        if (!empty($trans)) {
            $url = VNPAYService::refund("03", $paymentCode, $money, $trans->payment_date);
        } else {
            $url = config('frontend.transaction-fail');
        }

        return $this->successResponse(["url" => $url], "Successfully!");

    }

    public function getTransactions(Request $request)
    {
        $filter = $request->only('created_at', 'payment_code', 'status', 'payment_date', 'method', 'type');

        try {
            $transactionData = $this->transactionRepository->getTransactions($filter);

            return $this->successResponse($transactionData, "Successfully!");
        } catch (\Exception $exception) {
            Log::error("[ERROR]" . $exception->getMessage());
            return $this->errorResponse([], 'Server error', 500);
        }
    }

    public function statistic(Request $request)
    {
        $filter = [
            'start_at' => empty($request->start_at) ? null : $request->start_at,
            'end_at' => empty($request->end_at) ? null : $request->end_at
        ];

        try {
            $transaction = $this->transactionRepository->getStatistic($filter);
            $payment_method = $this->transactionRepository->getStatisticByPaymentMethod($filter);

            return $this->successResponse([
                'transaction' => $transaction,
                'method' => $payment_method
            ], "Successfully!");
        } catch (\Exception $exception) {
            Log::error("[ERROR]" . $exception->getMessage());
            return $this->errorResponse([], 'Server error', 500);
        }
    }

    public function getTransactionByPaymentCode(Request $request)
    {
        $paymentCode = $request->payment_code;

        try {
            $transactionData = $this->transactionRepository->findByPaymentCode($paymentCode);

            return $this->successResponse($transactionData, "Successfully!");
        } catch (\Exception $exception) {
            Log::error("[ERROR]" . $exception->getMessage());
            return $this->errorResponse([], 'Server error', 500);
        }
    }

    public function getTransactionByOrder(Request $request)
    {
        $orderId = $request->order_id;

        try {
            $transactionData = $this->transactionRepository->findByOrderId($orderId);

            return $this->successResponse($transactionData, "Successfully!");
        } catch (\Exception $exception) {
            Log::error("[ERROR]" . $exception->getMessage());
            return $this->errorResponse([], 'Server error', 500);
        }
    }

    public function changeStatus(Request $request, Transaction $transaction)
    {

        try {
            if (!empty($request->status) && $request->status === "paid") {
                $transaction = $this->transactionRepository->update([
                    "status" => $request->status
                ], $transaction['id']);
            }
            return $this->successResponse($transaction, "Successfully!");
        } catch (\Exception $exception) {
            Log::error("[ERROR]" . $exception->getMessage());
            return $this->errorResponse([], 'Server error', 500);
        }
    }
}
