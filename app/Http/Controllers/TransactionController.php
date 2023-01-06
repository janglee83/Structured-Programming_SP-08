<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Transaction;
use App\Repositories\InvoiceRepository;
use App\Repositories\TransactionRepository;
use App\Services\VNPAYService;
use Illuminate\Http\Request;

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
    public function processPayment(Request $request)
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

        $url = "http://mail.google.com";
        if ($method === "VNPay") {
            $url = VNPAYService::create_payment($trans->payment_code, $money, null);
        } else if ($method === "Shipcode") {
            $url = "http://localhost:8000/api/invoices/" . $invoice['id'] . "/status";
        }

        return redirect($url);
    }
}
