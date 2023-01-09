<?php

namespace App\Http\Controllers;

use App\Repositories\OrderDetailRepository;
use App\Repositories\TransactionRepository;
use App\Services\VNPAYService;
use Exception;
use Illuminate\Http\Request;

class VNPAYController extends Controller
{
    private $transactionRepository;

    public function __construct(TransactionRepository $transactionRepository)
    {
        $this->transactionRepository = $transactionRepository;
    }

    public function return(Request $request)
    {
        $transactionCode = $request->vnp_TxnRef;
        $bankCode = $request->vnp_BankCode;
        $money = (int) $request->vnp_Amount / 100;
        $transaction = $this->transactionRepository->findByPaymentCode($transactionCode);
        if ($request->vnp_ResponseCode == "00") {
            $status = "successful";
        } else
            $status = "failed";

        if (!empty($transaction)) {
            $this->transactionRepository->update([
                "bank_code" => strtolower($bankCode),
                "status" => $status,
                "payment_date" => $request->vnp_ResponseCode == "00" ? now() : null
            ], $transaction->id);
        }

        return redirect(config("frontend.url") . "/transactions/status-payment?payment_code=".$transactionCode."&money=".$money."&status=".$status);
    }

    public function ipn(Request $request)
    {
        return VNPAYService::ipn($request->all(), $this->transactionRepository);
    }
}
