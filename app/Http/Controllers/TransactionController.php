<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreatePaymentRequest;
use App\Models\Transaction;
use App\Repositories\InvoiceRepository;
use App\Repositories\TransactionRepository;
use App\Services\VNPAYService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
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

    private function createTransaction($data, $type = 'pay')
    {
        $trans = $this->transactionRepository->create($data);
        if ($type === 'pay') {
            $paymentCode = Transaction::generateCode($trans['id'], $trans['created_at']);
        } else {
            $paymentCode = Transaction::generateRefundCode($trans['id'], $trans['created_at']);
        }
        $trans = $this->transactionRepository->update([
            'payment_code' => $paymentCode,
        ], $trans['id']);

        return $trans;
    }
    public function processPayment(CreatePaymentRequest $request)
    {
        $money = $request->money;
        $method = $request->payment_method ?? "";
        $orderId = $request->order_id;

        $trans_data = [
            'customer_id' => $request->customer_id,
            'status' => 'pending',
            "order_id" => $orderId,
            'method' => $method,
            'money' => $money
        ];

        $trans = $this->createTransaction($trans_data, 'pay');

        if ($method === "vnpay") {
            $url = VNPAYService::create_payment($trans->payment_code, $money, "VNPAYQR");
        } else if ($method === "shipcod") {
            $url = config("frontend.url") . "/transactions/status-payment?payment_code="
                . $trans['payment_code'] . "&money=" . $money . "&status=" . $trans['status'];
        } else
            $url = VNPAYService::create_payment($trans->payment_code, $money, "VNBANK");
//            INTCARD

        return $this->successResponse(["url" => $url], "Successfully!");
    }

    public function refund(Request $request)
    {
        $orderId = $request->order_id;
        $money = $request->money;
        // TODO:: tìm payment_code
        $transaction = $this->transactionRepository->findSuccessByOrderId($orderId);
        $tranType = (int)$money < (int)$transaction['money'] ? "02" : "03";

        $money = (int)$money < (int)$transaction['method'] ? $money : $transaction["money"];

        if (!empty($transaction)) {
            $trans_data = [
                'customer_id' => $transaction['customer_id'],
                'status' => 'pending',
                "method" => $transaction['method'],
                "money" => $money,
                "order_id" => $orderId,
                "type" => "refund"
            ];

            $refundTrans = $this->createTransaction($trans_data, 'refund');

            $response = VNPAYService::refund($tranType, $transaction, $refundTrans);

            if ($response['vnp_ResponseCode'] == "00") {
                $date = date_create_from_format("YmdHis", $response['vnp_PayDate']);
                $status = "successful";
                $this->transactionRepository->update([
                    "bank_code" => strtolower($response['vnp_BankCode']),
                    "transaction_code" => $response['vnp_TransactionNo'],
                    "status" => $status,
                    "payment_date" => $date
                ], $refundTrans['id']);
            } else {
                $status = "failed";
                $this->transactionRepository->update([
                    "bank_code" => strtolower($response['vnp_BankCode']),
                    "status" => $status
                ], $refundTrans['id']);
            }

            $url = config("frontend.url") . "/transactions/status-payment?payment_code=".$refundTrans['payment_code']."&money=".$money."&status=".$status;
        } else {
            $url = config('frontend.url');
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
//            $transaction = $this->transactionRepository->getStatistic($filter);
            $payment_method = $this->transactionRepository->getStatisticByPaymentMethod($filter);
            if (empty($payment_method['vnpay']))
                $payment_method['vnpay'] = 0;
            if (empty($payment_method['atm']))
                $payment_method['atm'] = 0;
            if (empty($payment_method['shipcod']))
                $payment_method['shipcod'] = 0;

            return $this->successResponse([
//                'transaction' => $transaction,
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
        $orderId = (int) $request->order_id;

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
                    "status" => "successful"
                ], $transaction['id']);
            }
            return $this->successResponse($transaction, "Successfully!");
        } catch (\Exception $exception) {
            Log::error("[ERROR]" . $exception->getMessage());
            return $this->errorResponse([], 'Server error', 500);
        }
    }

    public function statisticByMonth(Request $request)
    {
        $filter = [
            'start_at' => empty($request->start_at) ? Carbon::now()->subMonths(5) : $request->start_at,
            'end_at' => empty($request->end_at) ? Carbon::now() : $request->end_at
        ];

        $filter = [
            "start_at" => Carbon::parse($filter['start_at'])->startOfMonth(),
            "end_at" => Carbon::parse($filter['end_at'])->endOfMonth(),
        ];

        try {
            $transactionsByMonth = $this->transactionRepository->getStatisticByMonth($filter);

            // Initialize the results array
            $results = [];
            $month = $filter['start_at'];
            for ($i = 1; $i <= 6; $i++) {
                $key = $month->format('Y-m');
                $results[$key] = [
                    'date' => $month->format('Y-m'),
                    'successful' => 0,
                    'failed' => 0,
                    'pending' => 0,
                    'total' => 0,
                    'success_percentage' => 0,
                    'fail_percentage' => 0,
                ];
                $month = $month->addMonth();
            }

            // Loop through the transactions and populate the results array
            foreach ($transactionsByMonth as $transaction) {
                $month = $transaction->month;
                if ($transaction->status == 'successful') {
                    $results[$month]['successful'] = $transaction->count;
                } else if ($transaction->status == 'failed') {
                    $results[$month]['failed'] = $transaction->count;
                } else if ($transaction->status == 'pending') {
                    $results[$month]['pending'] = $transaction->count;
                }
                $results[$month]['total'] = $results[$month]['successful'] + $results[$month]['failed'] + $results[$month]['pending'];
                if ($results[$month]['total'] > 0) {
                    $results[$month]['success_percentage'] = $results[$month]['successful'] / $results[$month]['total'] * 100;
                    $results[$month]['fail_percentage'] = $results[$month]['failed'] / $results[$month]['total'] * 100;
                }
            }

            return $this->successResponse(collect($results)->values(), "Successfully!");
        } catch (\Exception $exception) {
            Log::error("[ERROR]" . $exception->getMessage());
            return $this->errorResponse([], 'Server error', 500);
        }
    }

    public function statisticPayAndRefund(Request $request)
    {
        $filter = [
            'start_at' => empty($request->start_at) ? Carbon::now()->subMonths(1) : $request->start_at,
            'end_at' => empty($request->end_at) ? Carbon::now() : $request->end_at
        ];

        try {
            $transaction = $this->transactionRepository->getStatisticPayAndRefund($filter);
            $data = [
                'total_revenue' => (int) $transaction['total_revenue'],
                'total_cashback' => (int) $transaction['total_cashback']
            ];

            // Initialize the results array
            $results = [];
            $day = Carbon::parse($filter['start_at']);
            $dayOfMonth = Carbon::parse($filter['end_at'])->diffInDays(Carbon::parse($filter['start_at']));
            for ($i = 0; $i <= $dayOfMonth; $i++) {
                $key = $day->format('m-d');
                $results[$key] = [
                    'date' => $key,
                    'pay' => 0,
                    'refund' => 0,
                ];
                $day = $day->addDays();
            }

            // Loop through the transactions and populate the results array
            foreach ($transaction['transactions'] as $transaction) {
                $day = $transaction->day;
                $results[$day][$transaction->type] = (int) $transaction->total;
            }
            $data['results'] = collect($results)->values();

            return $this->successResponse($data, "Successfully!");
        } catch (\Exception $exception) {
            Log::error("[ERROR]" . $exception->getMessage());
            return $this->errorResponse([], 'Server error', 500);
        }
    }
}
