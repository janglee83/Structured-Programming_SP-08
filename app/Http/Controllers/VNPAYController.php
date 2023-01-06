<?php

namespace App\Http\Controllers;

use App\Repositories\OrderDetailRepository;
use App\Repositories\TransactionRepository;
use Illuminate\Http\Request;

class VNPAYController extends Controller
{
    public $orderDetailRepository;
    public $transactionRepository;

    public function __construct(
        TransactionRepository $transactionRepository,
        OrderDetailRepository $orderDetailRepository
    )
    {
        $this->transactionRepository = $transactionRepository;
        $this->orderDetailRepository = $orderDetailRepository;
    }

    public function returnVnpay(Request $request, OrderDetailRepository $orderDetailRepository)
    {
        $transactionCode = $request->vnp_TxnRef;
        $method = $request->vnp_CardType;
//        $transaction = $this->transactionRepository->find("code", $transactionCode)->first();
//
//        if (empty($transaction)) {
//            return abort(404);
//        }
//
//        $orderItems = $orderDetailRepository->find('order_id', $transaction['order_id']);
//        if (!empty($orderItems)) {
//
//        }
//
//        $this->transactionRepository->update(["method" => strtolower($method)], $transaction->id);
//        return redirect('/subscription/transaction-status/' . $transaction->id);
    }

    public function ipn(Request $request)
    {
//        return VNPAYService::ipn($request->all(), $this->transactionRepository, $this->orderItemRepository, $this->enrollRepository);
    }
}
