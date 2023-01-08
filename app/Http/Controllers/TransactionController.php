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
                $results[$month->format('Y-m')] = [
                    'successful' => 0,
                    'failed' => 0,
                    'total' => 0,
                    'success_percentage' => 0,
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
                }
                $results[$month]['total'] = $results[$month]['successful'] + $results[$month]['failed'];
                if ($results[$month]['total'] > 0) {
                    $results[$month]['success_percentage'] = $results[$month]['successful'] / $results[$month]['total'] * 100;
                }
            }

            return $this->successResponse($results, "Successfully!");
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
                $results[$day->format('m-d')] = [
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
            $data['results'] = $results;

            return $this->successResponse($data, "Successfully!");
        } catch (\Exception $exception) {
            Log::error("[ERROR]" . $exception->getMessage());
            return $this->errorResponse([], 'Server error', 500);
        }
    }
}
